<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\StatusTransition;
use App\Models\StudyApplication;
use App\Models\User;
use App\Models\VisaApplication;
use App\Services\ApplicationStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Applications, Documents (per-application context), Admission, and Visa —
 * grouped here because all four are tightly coupled to a single
 * StudyApplication, matching the workspace tab consolidation explained in
 * StudentWorkspaceController's docblock.
 */
class ApplicationController extends Controller
{
    public function changeStatus(Request $request, User $student, StudyApplication $application, ApplicationStatusService $service): RedirectResponse
    {
        $this->authorize('changeStatus', $application);
        abort_unless($application->student_id === $student->id, 404);

        $request->validate([
            'to_status_id' => ['required', 'exists:statuses,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $service->transition($application, $request->integer('to_status_id'), $request->user(), $request->input('note'));
        } catch (\App\Exceptions\InvalidStatusTransitionException $e) {
            return back()->withErrors(['to_status_id' => 'That status change is not allowed from the current stage.']);
        }

        return back()->with('success', 'Application status updated.');
    }

    public function assignOfficer(Request $request, User $student, StudyApplication $application): RedirectResponse
    {
        $this->authorize('assignOfficer', $application);
        abort_unless($application->student_id === $student->id, 404);

        $request->validate(['officer_id' => ['required', 'exists:users,id']]);

        $application->update(['assigned_officer_id' => $request->integer('officer_id')]);

        return back()->with('success', 'Officer assigned.');
    }

    public function updateAdmission(Request $request, User $student, StudyApplication $application): RedirectResponse
    {
        $this->authorize('update', $application);
        abort_unless($application->student_id === $student->id, 404);

        $request->validate([
            'decision' => ['required', Rule::in(['pending', 'offered', 'accepted', 'rejected'])],
            'conditions' => ['nullable', 'string', 'max:1000'],
            'offer_letter_document_id' => ['nullable', 'exists:documents,id'],
        ]);

        $application->admission()->updateOrCreate([], [
            'decision' => $request->string('decision'),
            'conditions' => $request->input('conditions'),
            'offer_letter_document_id' => $request->input('offer_letter_document_id'),
            'decided_at' => in_array($request->input('decision'), ['accepted', 'rejected']) ? now() : null,
        ]);

        return back()->with('success', 'Admission details updated.');
    }

    /**
     * One form handles: creating the VisaApplication if it doesn't exist yet,
     * transitioning its status (via the same DB-driven engine as applications),
     * and updating embassy appointment / decision — all in a single submission,
     * since these are all "the visa section" from the Admin's point of view.
     */
    public function updateVisa(Request $request, User $student, StudyApplication $application, ApplicationStatusService $service): RedirectResponse
    {
        $visa = $application->visaApplication;

        // No VisaApplication yet? Authorize against the parent application instead —
        // there's no visa instance to check ownership/permissions against yet.
        $visa ? $this->authorize('update', $visa) : $this->authorize('update', $application);
        abort_unless($application->student_id === $student->id, 404);

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
                'study_application_id' => $application->id,
                'destination_country' => $request->string('destination_country') ?: $application->university->country,
                'status_id' => $initialStatusId,
            ]);
        }

        if ($request->filled('to_status_id') && (int) $request->input('to_status_id') !== $visa->status_id) {
            try {
                $service->transition($visa, $request->integer('to_status_id'), $request->user(), $request->input('note'));
            } catch (\App\Exceptions\InvalidStatusTransitionException $e) {
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
