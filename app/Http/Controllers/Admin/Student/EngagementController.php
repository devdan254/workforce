<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAppointmentRequest;
use App\Http\Requests\Admin\StoreNoteRequest;
use App\Http\Requests\Admin\StoreSupportReplyRequest;
use App\Http\Requests\Admin\StoreSupportTicketRequest;
use App\Http\Requests\Admin\StoreTaskRequest;
use App\Http\Requests\Admin\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Note;
use App\Models\StudentProfile;
use App\Models\SupportTicket;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Appointments, Messages (support tickets), Tasks, and Notes — grouped
 * together per the workspace's tab organization. These are the "operational
 * housekeeping" tabs: nothing here changes an application's core state,
 * unlike ApplicationController's actions.
 */
class EngagementController extends Controller
{
    /**
     * Admin booking an appointment ON BEHALF OF a student — lives in the
     * Appointments tab (not a global admin menu item), since an appointment
     * is fundamentally scoped to one student. A cross-student calendar/list
     * view would be a genuinely different feature, not built here.
     */
    public function storeAppointment(StoreAppointmentRequest $request, User $student): RedirectResponse
    {
        Appointment::create([
            'student_id' => $student->id,
            'staff_id' => $request->input('staff_id') ?? $request->user()->id,
            'study_application_id' => $request->input('study_application_id'),
            'type' => $request->string('type'),
            'mode' => $request->string('mode'),
            'scheduled_at' => $request->date('scheduled_at'),
            // Admin-booked appointments start confirmed — unlike a student's own
            // request (which starts as "requested" awaiting staff confirmation),
            // staff booking it themselves IS the confirmation.
            'status' => 'confirmed',
            'notes' => $request->input('notes'),
        ]);

        return back()->with('success', 'Appointment booked and confirmed.');
    }

    public function confirmAppointment(Request $request, User $student, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        abort_unless($appointment->student_id === $student->id, 404);

        $appointment->update([
            'status' => 'confirmed',
            'staff_id' => $appointment->staff_id ?? $request->user()->id,
        ]);

        return back()->with('success', 'Appointment confirmed.');
    }

    public function cancelAppointment(Request $request, User $student, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        abort_unless($appointment->student_id === $student->id, 404);

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }

    public function completeAppointment(Request $request, User $student, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        abort_unless($appointment->student_id === $student->id, 404);

        $appointment->update(['status' => 'completed']);

        return back()->with('success', 'Appointment marked completed.');
    }

    /**
     * Editing an existing appointment — unlike the student's own reschedule
     * (which resets to "requested" for re-confirmation), Admin editing it
     * does NOT reset status. Admin IS the confirming authority, so directly
     * correcting the time/staff/type doesn't need Admin to re-confirm their
     * own change.
     */
    public function updateAppointment(UpdateAppointmentRequest $request, User $student, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->student_id === $student->id, 404);

        $appointment->update([
            'type' => $request->string('type'),
            'mode' => $request->string('mode'),
            'scheduled_at' => $request->date('scheduled_at'),
            'staff_id' => $request->input('staff_id') ?? $appointment->staff_id,
        ]);

        return back()->with('success', 'Appointment updated.');
    }

    /**
     * Admin PROACTIVELY opening a new ticket TO a student — distinct from
     * replyTicket() below, which responds to a ticket the student already
     * raised. Starts at "waiting_for_student" since staff spoke first.
     */
    public function storeTicket(StoreSupportTicketRequest $request, User $student): RedirectResponse
    {
        $ticket = SupportTicket::create([
            'student_id' => $student->id,
            'category' => $request->string('category'),
            'priority' => $request->string('priority'),
            'subject' => $request->string('subject'),
            'status' => 'waiting_for_student',
            'assigned_to' => $request->user()->id,
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('message'),
        ]);

        return back()->with('success', "Message sent to {$student->name}.");
    }

    /**
     * Replying sets the ticket to "waiting_for_student" — staff has now
     * spoken, the ball is in the student's court. Mirrors the student-side
     * SupportTicketController::reply(), which does the inverse (re-opens to
     * in_progress when a student replies to a resolved/waiting ticket).
     */
    public function replyTicket(StoreSupportReplyRequest $request, User $student, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->student_id === $student->id, 404);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('body'),
        ]);

        if (! in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'waiting_for_student']);
        }

        return back()->with('success', 'Reply sent.');
    }

    public function updateTicketStatus(Request $request, User $student, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->student_id === $student->id, 404);

        $request->validate(['status' => ['required', Rule::in(['open', 'in_progress', 'waiting_for_student', 'resolved', 'closed'])]]);

        $ability = $request->input('status') === 'closed' ? 'close' : 'respond';
        $this->authorize($ability, $ticket);

        $ticket->update(['status' => $request->string('status')]);

        return back()->with('success', 'Ticket status updated.');
    }

    public function storeTask(StoreTaskRequest $request, User $student): RedirectResponse
    {
        // A student may not have a profile yet (only created on first application) —
        // Admin adding a task shouldn't be blocked by that, so create a bare one.
        $profile = $student->studentProfile ?? $student->studentProfile()->create([]);

        Task::create([
            'taskable_type' => StudentProfile::class,
            'taskable_id' => $profile->id,
            'title' => $request->string('title'),
            'description' => $request->input('description'),
            'assigned_to' => $request->integer('assigned_to'),
            'due_date' => $request->input('due_date'),
            'priority' => $request->string('priority'),
            'status' => 'pending',
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Task created.');
    }

    public function completeTask(Request $request, User $student, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->update(['status' => 'completed']);

        return back()->with('success', 'Task marked completed.');
    }

    public function storeNote(StoreNoteRequest $request, User $student): RedirectResponse
    {
        $profile = $student->studentProfile ?? $student->studentProfile()->create([]);

        Note::create([
            'noteable_type' => StudentProfile::class,
            'noteable_id' => $profile->id,
            'body' => $request->string('body'),
            'is_internal' => $request->boolean('is_internal', true),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Note added.');
    }
}
