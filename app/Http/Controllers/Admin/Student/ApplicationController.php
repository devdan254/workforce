<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreApplicationRequest;
use App\Http\Requests\Admin\UpdateApplicationRequest;
use App\Models\Course;
use App\Models\Status;
use App\Models\StatusTransition;
use App\Models\StudyApplication;
use App\Models\University;
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
    /**
     * Admin creating an application ON BEHALF of a student — mirrors the
     * student's own ApplicationController::store(), except University and
     * Course are free-text here rather than a catalog dropdown. Admin
     * routinely needs to log an application to a university that isn't in
     * the system yet; firstOrCreate() means the catalog grows organically
     * from real admin usage instead of needing to be pre-seeded exhaustively.
     */
    public function store(StoreApplicationRequest $request, User $student): RedirectResponse
    {
        abort_unless($student->hasRole('student'), 404);

        $university = University::firstOrCreate(
            ['name' => $request->string('university_name')],
            ['country' => $request->string('university_country'), 'is_active' => true]
        );

        $course = Course::firstOrCreate(
            ['university_id' => $university->id, 'name' => $request->string('course_name')],
            ['study_level' => $request->string('study_level'), 'is_active' => true]
        );

        $startStatus = Status::where('type', 'application')->where('slug', 'application_started')->firstOrFail();

        $application = StudyApplication::create([
            'student_id' => $student->id,
            'university_id' => $university->id,
            'course_id' => $course->id,
            'status_id' => $startStatus->id,
            'intake' => $request->input('intake'),
            'application_deadline' => $request->input('application_deadline'),
            'assigned_officer_id' => $request->user()->id,
            'application_fee' => $request->input('application_fee', 0),
            'tuition_fee' => $request->input('tuition_fee', 0),
            'service_fee' => $request->input('service_fee', 0),
            'currency' => $request->input('currency') ?: 'KES',
            'submitted_at' => now(),
        ]);

        return back()->with('success', "Application to {$university->name} created for {$student->name}.");
    }

    public function update(UpdateApplicationRequest $request, User $student, StudyApplication $application): RedirectResponse
    {
        abort_unless($application->student_id === $student->id, 404);

        $university = University::firstOrCreate(
            ['name' => $request->string('university_name')],
            ['country' => $request->string('university_country'), 'is_active' => true]
        );

        $course = Course::firstOrCreate(
            ['university_id' => $university->id, 'name' => $request->string('course_name')],
            ['study_level' => $request->string('study_level'), 'is_active' => true]
        );

        $application->update([
            'university_id' => $university->id,
            'course_id' => $course->id,
            'intake' => $request->input('intake'),
            'application_deadline' => $request->input('application_deadline'),
            'application_fee' => $request->input('application_fee', $application->application_fee),
            'tuition_fee' => $request->input('tuition_fee', $application->tuition_fee),
            'service_fee' => $request->input('service_fee', $application->service_fee),
            'currency' => $request->input('currency') ?: $application->currency,
        ]);

        return back()->with('success', 'Application details updated.');
    }

    public function destroy(Request $request, User $student, StudyApplication $application): RedirectResponse
    {
        $this->authorize('delete', $application);
        abort_unless($application->student_id === $student->id, 404);

        $application->delete(); // soft delete — StudyApplication uses SoftDeletes

        return redirect()->route('admin.students.show', $student)->with('success', 'Application deleted.');
    }

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
