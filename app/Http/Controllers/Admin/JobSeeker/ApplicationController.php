<?php

namespace App\Http\Controllers\Admin\JobSeeker;

use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJobSeekerApplicationRequest;
use App\Models\Appointment;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Status;
use App\Models\StatusTransition;
use App\Models\User;
use App\Models\VisaApplication;
use App\Services\ApplicationStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Applications, Interviews, Job Offers, and Visa — grouped here exactly like
 * Admin\Student\ApplicationController groups Applications/Documents/Admission/
 * Visa, since all four are tightly coupled to a single JobApplication.
 *
 * The genuine differences from Student's version, not just renamed copies:
 * - store() picks an EXISTING job_posting (dropdown), not free-text
 *   university/course — job postings are Altura's controlled catalog.
 * - No fee fields on creation — job_applications deliberately has none
 *   (Stage 2's ad-hoc-invoice-only fee model, decided early on).
 * - Interview scheduling and Job Offer management are genuinely new (Student
 *   has neither) — Interview reuses the shared Appointment table/Policy
 *   unmodified; Offer is a new small model this app didn't have before.
 */
class ApplicationController extends Controller
{
    public function store(StoreJobSeekerApplicationRequest $request, User $jobSeeker): RedirectResponse
    {
        abort_unless($jobSeeker->hasRole('job_seeker'), 404);

        $startStatus = Status::where('type', 'job_application')->where('slug', 'application_submitted')->firstOrFail();

        $application = JobApplication::create([
            'job_seeker_id' => $jobSeeker->id,
            'job_posting_id' => $request->integer('job_posting_id'),
            'status_id' => $startStatus->id,
            'assigned_officer_id' => $request->user()->id,
            'applied_at' => now(),
        ]);

        return back()->with('success', "Application to \"{$application->jobPosting->title}\" created for {$jobSeeker->name}.");
    }

    public function destroy(Request $request, User $jobSeeker, JobApplication $application): RedirectResponse
    {
        $this->authorize('delete', $application);
        abort_unless($application->job_seeker_id === $jobSeeker->id, 404);

        $application->delete(); // soft delete — JobApplication uses SoftDeletes

        return redirect()->route('admin.job-seekers.show', $jobSeeker)->with('success', 'Application deleted.');
    }

    public function changeStatus(Request $request, User $jobSeeker, JobApplication $application, ApplicationStatusService $service): RedirectResponse
    {
        $this->authorize('changeStatus', $application);
        abort_unless($application->job_seeker_id === $jobSeeker->id, 404);

        $request->validate([
            'to_status_id' => ['required', 'exists:statuses,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $service->transition($application, $request->integer('to_status_id'), $request->user(), $request->input('note'));
        } catch (InvalidStatusTransitionException $e) {
            return back()->withErrors(['to_status_id' => 'That status change is not allowed from the current stage.']);
        }

        return back()->with('success', 'Application status updated.');
    }

    public function assignOfficer(Request $request, User $jobSeeker, JobApplication $application): RedirectResponse
    {
        $this->authorize('assignOfficer', $application);
        abort_unless($application->job_seeker_id === $jobSeeker->id, 404);

        $request->validate(['officer_id' => ['required', 'exists:users,id']]);

        $application->update(['assigned_officer_id' => $request->integer('officer_id')]);

        return back()->with('success', 'Officer assigned.');
    }

    /**
     * Interview scheduling — reuses the shared Appointment table/Policy
     * completely unmodified (AppointmentPolicy::create already allows any
     * staff with appointments.create). type is hardcoded 'Interview' since
     * that's specifically what this action is for; general appointment
     * booking (consultations etc.) lands with the Appointments tab delivery.
     */
    public function scheduleInterview(Request $request, User $jobSeeker, JobApplication $application): RedirectResponse
    {
        $this->authorize('create', Appointment::class);
        abort_unless($application->job_seeker_id === $jobSeeker->id, 404);

        $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'mode' => ['required', Rule::in(['online', 'in_person', 'phone'])],
            'meeting_link' => ['nullable', 'url', 'max:255'],
            'staff_id' => ['nullable', 'exists:users,id'],
        ]);

        Appointment::create([
            'student_id' => $jobSeeker->id,
            'staff_id' => $request->input('staff_id') ?: $request->user()->id,
            'job_application_id' => $application->id,
            'type' => 'Interview',
            'mode' => $request->string('mode'),
            'meeting_link' => $request->input('meeting_link'),
            'scheduled_at' => $request->date('scheduled_at'),
            'status' => 'confirmed', // admin-booked starts confirmed, same convention as Student's appointments
        ]);

        return back()->with('success', 'Interview scheduled.');
    }

    /**
     * One form creates the offer if none exists yet, or updates its terms
     * if it does — same "one form handles create-or-update" pattern as
     * Student's updateAdmission(). Authorized against the PARENT application
     * (job_applications.update) rather than a JobOffer-specific ability,
     * since at creation time there's no JobOffer instance yet to check.
     */
    public function storeOrUpdateOffer(Request $request, User $jobSeeker, JobApplication $application): RedirectResponse
    {
        $this->authorize('update', $application);
        abort_unless($application->job_seeker_id === $jobSeeker->id, 404);

        $request->validate([
            'salary' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'benefits' => ['nullable', 'string', 'max:1000'],
            'working_hours' => ['nullable', 'string', 'max:100'],
            'accommodation_provided' => ['nullable', 'boolean'],
            'meals_provided' => ['nullable', 'boolean'],
            'air_ticket_provided' => ['nullable', 'boolean'],
            'contract_duration' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:150'],
            'offer_letter_document_id' => ['nullable', 'exists:documents,id'],
        ]);

        $isNew = ! $application->offer;

        $offer = $application->offer()->updateOrCreate([], [
            'salary' => $request->input('salary'),
            'currency' => $request->string('currency'),
            'benefits' => $request->input('benefits'),
            'working_hours' => $request->input('working_hours'),
            'accommodation_provided' => $request->boolean('accommodation_provided'),
            'meals_provided' => $request->boolean('meals_provided'),
            'air_ticket_provided' => $request->boolean('air_ticket_provided'),
            'contract_duration' => $request->input('contract_duration'),
            'start_date' => $request->input('start_date'),
            'location' => $request->input('location'),
            'offer_letter_document_id' => $request->input('offer_letter_document_id'),
        ]);

        return back()->with('success', $isNew ? 'Offer created.' : 'Offer updated.');
    }

    /**
     * Sending the offer is a distinct step from saving its terms — mirrors
     * how Invoice has draft->sent as separate actions. Sets sent_at and
     * transitions the application to offer_extended via the same
     * ApplicationStatusService every other status change uses.
     */
    public function sendOffer(Request $request, User $jobSeeker, JobApplication $application, ApplicationStatusService $service): RedirectResponse
    {
        $this->authorize('update', $application);
        abort_unless($application->job_seeker_id === $jobSeeker->id, 404);
        abort_unless($application->offer, 404, 'No offer exists yet for this application.');

        $application->offer->update(['sent_at' => now()]);

        $extendedStatus = Status::where('type', 'job_application')->where('slug', 'offer_extended')->first();
        if ($extendedStatus && $application->status_id !== $extendedStatus->id) {
            try {
                $service->transition($application, $extendedStatus->id, $request->user(), 'Offer sent to candidate.');
            } catch (InvalidStatusTransitionException $e) {
                // Offer still gets sent even if the application was already past this point.
            }
        }

        $jobSeeker->notify(new \App\Notifications\JobOfferSentNotification($application->offer));

        return back()->with('success', 'Offer sent to candidate.');
    }

    public function updateVisa(Request $request, User $jobSeeker, JobApplication $application, ApplicationStatusService $service): RedirectResponse
    {
        $visa = $application->visaApplication;

        $visa ? $this->authorize('update', $visa) : $this->authorize('update', $application);
        abort_unless($application->job_seeker_id === $jobSeeker->id, 404);

        $request->validate([
            'destination_country' => ['required_without:existing_visa', 'nullable', 'string', 'max:100'],
            'to_status_id' => ['nullable', 'exists:statuses,id'],
            'embassy_appointment_at' => ['nullable', 'date'],
            'decision' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! $visa) {
            $initialStatusId = StatusTransition::where('status_type', 'visa')->whereNull('from_status_id')->value('to_status_id');

            $visa = VisaApplication::create([
                'job_application_id' => $application->id,
                'destination_country' => $request->string('destination_country') ?: $application->jobPosting->country,
                'status_id' => $initialStatusId,
            ]);
        }

        if ($request->filled('to_status_id') && (int) $request->input('to_status_id') !== $visa->status_id) {
            try {
                $service->transition($visa, $request->integer('to_status_id'), $request->user(), $request->input('note'));
            } catch (InvalidStatusTransitionException $e) {
                return back()->withErrors(['to_status_id' => 'That visa status change is not allowed from the current stage.']);
            }
        }

        $updates = array_filter([
            'embassy_appointment_at' => $request->input('embassy_appointment_at'),
            'decision' => $request->input('decision'),
        ], fn ($v) => ! is_null($v) && $v !== '');

        if ($updates) {
            $visa->fresh()->update($updates);
        }

        if (in_array($request->input('decision'), ['approved', 'rejected']) && ! $visa->decided_at) {
            $visa->fresh()->update(['decided_at' => now()]);
        }

        return back()->with('success', 'Visa details updated.');
    }
}
