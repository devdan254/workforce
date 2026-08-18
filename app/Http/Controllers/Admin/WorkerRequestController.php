<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConvertWorkerRequestRequest;
use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Models\WorkerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * The literal "Altura Review" step of Employer → Worker Request → Altura →
 * Official Job Posting. Deliberately does NOT duplicate JobPostingController
 * — convertToJobPosting() creates a draft posting, now carrying over every
 * structured field the Worker Request actually collects (salary,
 * responsibilities, skills, benefits, requirements — no longer just a bare
 * title), then hands off to the EXISTING JobPostingController::edit()/
 * update()/publish() for the rest (description, deadline, image). Admin
 * still publishes from that same page, same button, same Policy check.
 */
class WorkerRequestController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', WorkerRequest::class);

        $query = WorkerRequest::with(['employer.employerProfile', 'jobPosting']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('admin.worker-requests.index', ['requests' => $requests]);
    }

    public function show(WorkerRequest $workerRequest): View
    {
        $this->authorize('view', $workerRequest);

        $workerRequest->load(['employer.employerProfile', 'reviewedBy', 'jobPosting']);

        return view('admin.worker-requests.show', [
            'workerRequest' => $workerRequest,
            'categories' => JobCategory::orderBy('name')->get(),
        ]);
    }

    public function review(Request $request, WorkerRequest $workerRequest): RedirectResponse
    {
        $this->authorize('review', $workerRequest);

        $request->validate([
            'status' => ['required', Rule::in(['under_review', 'clarification_required', 'approved', 'rejected'])],
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $workerRequest->update([
            'status' => $request->string('status'),
            'review_notes' => $request->input('review_notes'),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Worker request updated.');
    }

    public function convertToJobPosting(ConvertWorkerRequestRequest $request, WorkerRequest $workerRequest): RedirectResponse
    {
        abort_if($workerRequest->job_posting_id, 409, 'This request has already been converted.');

        $posting = DB::transaction(function () use ($request, $workerRequest) {
            $posting = JobPosting::create([
                'job_category_id' => $request->integer('job_category_id'),
                'employer_id' => $workerRequest->employer_id,
                'title' => $workerRequest->job_title,
                'country' => $request->string('country'),
                'vacancies' => $workerRequest->quantity,
                'employment_type' => $workerRequest->employment_type,
                'experience_required' => $workerRequest->preferred_experience,
                'salary_min' => $workerRequest->salary_min,
                'salary_max' => $workerRequest->salary_max,
                'currency' => $workerRequest->currency ?: 'USD',
                'responsibilities' => $workerRequest->responsibilities,
                'skills' => $workerRequest->skills,
                'benefits' => $workerRequest->benefits,
                'requirements' => $workerRequest->requirements,
                'status' => 'draft',
                'posted_by' => $request->user()->id,
            ]);

            $workerRequest->update([
                'status' => 'converted',
                'job_posting_id' => $posting->id,
            ]);

            return $posting;
        });

        return redirect()
            ->route('admin.job-postings.edit', $posting)
            ->with('success', "Draft job posting created from this request — review the details, add a description, and publish when ready.");
    }
}
