<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobSeeker\StoreWizardStepRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Status;
use App\Services\ApplicationStatusService;
use App\Services\CvParsingService;
use App\Services\DocumentVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Session-backed wizard — nothing is written to the database until final
 * submission EXCEPT documents, which are created immediately on upload
 * (job_application_id null until submit) so files are never lost if the
 * job seeker abandons the wizard partway through. This reuses
 * DocumentVerificationService and ApplicationStatusService completely
 * unmodified — the same services Student's own flows use.
 */
class ApplicationWizardController extends Controller
{
    private const TOTAL_STEPS = 7;

    public function start(JobPosting $jobPosting): RedirectResponse
    {
        abort_unless($jobPosting->status === 'open', 404);

        if (auth()->user()->jobApplications()->where('job_posting_id', $jobPosting->id)->exists()) {
            return redirect()->route('job-seeker.jobs.show', $jobPosting)
                ->with('info', 'You have already applied to this job.');
        }

        return redirect()->route('job-seeker.jobs.apply.step', [$jobPosting, 1]);
    }

    public function showStep(JobPosting $jobPosting, int $step): View
    {
        abort_unless($step >= 1 && $step <= self::TOTAL_STEPS, 404);

        $data = $this->wizardData($jobPosting);

        // Prefill Personal Information from the account on a fresh start —
        // no reason to make them retype what we already have.
        if ($step === 1 && empty($data['personal'])) {
            $user = auth()->user();
            $data['personal'] = array_filter([
                'full_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]);
        }

        return view("job-seeker.apply.step{$step}", [
            'jobPosting' => $jobPosting,
            'step' => $step,
            'totalSteps' => self::TOTAL_STEPS,
            'data' => $data,
            'categories' => $step === 1 ? null : null,
        ]);
    }

    public function saveStep(StoreWizardStepRequest $request, JobPosting $jobPosting, int $step): RedirectResponse
    {
        abort_unless($step >= 1 && $step <= self::TOTAL_STEPS, 404);

        $data = $this->wizardData($jobPosting);

        if ($step === 4) {
            // Multiple experience rows — array-indexed inputs from "+ Add Another Experience".
            $occupations = $request->input('occupation', []);
            $employers = $request->input('employer', []);
            $years = $request->input('years_of_experience', []);

            $experiences = [];
            foreach ($occupations as $i => $occupation) {
                if (blank($occupation)) {
                    continue;
                }
                $experiences[] = [
                    'occupation' => $occupation,
                    'employer' => $employers[$i] ?? null,
                    'years_of_experience' => $years[$i] ?? null,
                ];
            }
            $data['experiences'] = $experiences;
        } else {
            $stepKey = match ($step) {
                1 => 'personal', 2 => 'address', 3 => 'education', 6 => 'additional', default => null,
            };
            if ($stepKey) {
                $data[$stepKey] = $request->validated();
            }
        }

        $this->saveWizardData($jobPosting, $data);

        return redirect()->route('job-seeker.jobs.apply.step', [$jobPosting, min($step + 1, self::TOTAL_STEPS)]);
    }

    /**
     * Step 1's optional "Upload CV to auto-fill" — this is where CV parsing
     * happens, per the spec's Section 10. The CV itself is saved as a real
     * Document immediately (satisfies Step 5's CV requirement too, so the
     * job seeker never has to upload it twice), and extraction results are
     * merged into the session data for the form to show as pre-filled,
     * editable values — never auto-submitted, always reviewable.
     */
    public function parseCv(Request $request, JobPosting $jobPosting, CvParsingService $parser, DocumentVerificationService $docService): RedirectResponse
    {
        $request->validate(['cv' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx']]);

        $file = $request->file('cv');
        $path = $file->store('documents/'.auth()->id(), 'public');

        $document = $this->createDocument('CV / Resume', 'employment-documents');
        $docService->markUploaded($document, $path, $file->getClientMimeType(), $file->getSize());

        $data = $this->wizardData($jobPosting);
        $data['documents']['cv_document_id'] = $document->id;

        $extracted = [];
        try {
            $extracted = $parser->extract(Storage::disk('public')->path($path), $file->getClientMimeType());
        } catch (\Throwable $e) {
            // CvParsingService already catches internally, but belt-and-braces —
            // a parsing failure must NEVER block the wizard.
        }

        if ($extracted) {
            $data['personal'] = array_merge($data['personal'] ?? [], $extracted);
            $message = 'We found some information from your CV — please review it below before continuing.';
        } else {
            $message = "CV uploaded. We couldn't automatically extract details from it, but that's fine — just fill in the form below.";
        }

        $this->saveWizardData($jobPosting, $data);

        return redirect()->route('job-seeker.jobs.apply.step', [$jobPosting, 1])->with('info', $message);
    }

    public function uploadDocument(Request $request, JobPosting $jobPosting, string $slot, DocumentVerificationService $docService): RedirectResponse
    {
        $slots = [
            'passport' => ['name' => 'Passport', 'category' => 'identity-documents'],
            'certificates' => ['name' => 'Academic Certificates', 'category' => 'academic-documents'],
            'license' => ['name' => 'Professional License', 'category' => 'employment-documents'],
            'photo' => ['name' => 'Passport Photo', 'category' => 'identity-documents'],
        ];
        abort_unless(isset($slots[$slot]), 404);

        $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png']]);

        $file = $request->file('file');
        $path = $file->store('documents/'.auth()->id(), 'public');

        $document = $this->createDocument($slots[$slot]['name'], $slots[$slot]['category']);
        $docService->markUploaded($document, $path, $file->getClientMimeType(), $file->getSize());

        $data = $this->wizardData($jobPosting);
        $data['documents'][$slot.'_document_id'] = $document->id;
        $this->saveWizardData($jobPosting, $data);

        return redirect()->route('job-seeker.jobs.apply.step', [$jobPosting, 5])
            ->with('success', "{$slots[$slot]['name']} uploaded.");
    }

    public function submit(Request $request, JobPosting $jobPosting): RedirectResponse
    {
        $request->validate(['declaration' => ['accepted']]);

        $data = $this->wizardData($jobPosting);

        // CV and Passport Photo are the two starred (*) requirements in the spec's Step 5.
        if (empty($data['documents']['cv_document_id']) || empty($data['documents']['photo_document_id'])) {
            return redirect()->route('job-seeker.jobs.apply.step', [$jobPosting, 5])
                ->withErrors(['documents' => 'CV/Resume and Passport Photo are both required before you can submit.']);
        }

        $user = $request->user();

        $application = DB::transaction(function () use ($user, $jobPosting, $data) {
            if (! empty($data['personal']['phone'])) {
                $user->update(['phone' => $data['personal']['phone']]);
            }

            $profile = $user->jobSeekerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth' => $data['personal']['date_of_birth'] ?? null,
                    'gender' => $data['personal']['gender'] ?? null,
                    'nationality' => $data['personal']['nationality'] ?? null,
                    'address_line' => $data['address']['address_line'] ?? null,
                    'city' => $data['address']['city'] ?? $data['address']['county'] ?? null,
                    'country' => $data['address']['country'] ?? null,
                    'preferred_countries' => ! empty($data['additional']['preferred_country']) ? [$data['additional']['preferred_country']] : null,
                    'preferred_industries' => ! empty($data['additional']['preferred_industry']) ? [$data['additional']['preferred_industry']] : null,
                    'expected_salary' => $data['additional']['expected_salary'] ?? null,
                    'earliest_availability' => $data['additional']['earliest_availability'] ?? null,
                    'worked_abroad_before' => $data['additional']['worked_abroad_before'] ?? null,
                ]
            );
            $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

            if (! empty($data['education'])) {
                $profile->educations()->create([
                    'level' => $data['education']['level'],
                    'institution' => $data['education']['institution'] ?? null,
                    'course' => $data['education']['course'] ?? null,
                    'graduation_year' => $data['education']['graduation_year'] ?? null,
                ]);
            }

            foreach ($data['experiences'] ?? [] as $i => $experience) {
                $profile->experiences()->create([
                    'occupation' => $experience['occupation'],
                    'employer' => $experience['employer'] ?? null,
                    'years_of_experience' => $experience['years_of_experience'] ?? null,
                    'is_current' => $i === 0,
                    'sort_order' => $i,
                ]);
            }

            $startStatus = Status::where('type', 'job_application')->where('slug', 'application_submitted')->firstOrFail();

            $application = JobApplication::create([
                'job_seeker_id' => $user->id,
                'job_posting_id' => $jobPosting->id,
                'status_id' => $startStatus->id,
                'applied_at' => now(),
            ]);

            // Attach every document uploaded during the wizard to this application.
            foreach ($data['documents'] ?? [] as $documentId) {
                Document::where('id', $documentId)->update(['job_application_id' => $application->id]);
            }

            return $application;
        });

        session()->forget("job_application_wizard.{$jobPosting->id}");

        return redirect()->route('job-seeker.dashboard')
            ->with('success', "Application submitted! Reference: {$application->reference_number}. Our team will review it shortly.");
    }

    private function createDocument(string $name, string $categorySlug): Document
    {
        $category = DocumentCategory::where('slug', $categorySlug)->first();

        return Document::create([
            'student_id' => auth()->id(),
            'document_category_id' => $category?->id,
            'name' => $name,
            'status' => 'required',
        ]);
    }

    private function wizardData(JobPosting $jobPosting): array
    {
        return session()->get("job_application_wizard.{$jobPosting->id}", []);
    }

    private function saveWizardData(JobPosting $jobPosting, array $data): void
    {
        session()->put("job_application_wizard.{$jobPosting->id}", $data);
    }
}
