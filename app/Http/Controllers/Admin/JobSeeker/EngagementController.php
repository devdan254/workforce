<?php

namespace App\Http\Controllers\Admin\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJobSeekerAppointmentRequest;
use App\Http\Requests\Admin\StoreNoteRequest;
use App\Http\Requests\Admin\StoreSupportReplyRequest;
use App\Http\Requests\Admin\StoreSupportTicketRequest;
use App\Http\Requests\Admin\StoreTaskRequest;
use App\Http\Requests\Admin\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\JobSeekerProfile;
use App\Models\Note;
use App\Models\SupportTicket;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Mirrors Admin\Student\EngagementController almost exactly. Genuinely
 * reuses UpdateAppointmentRequest, StoreSupportTicketRequest,
 * StoreSupportReplyRequest, and StoreTaskRequest completely unmodified —
 * all four were already fully generic. Only StoreAppointmentRequest needed
 * a Job-Seeker variant (job_application_id instead of study_application_id).
 */
class EngagementController extends Controller
{
    public function storeAppointment(StoreJobSeekerAppointmentRequest $request, User $jobSeeker): RedirectResponse
    {
        Appointment::create([
            'student_id' => $jobSeeker->id,
            'staff_id' => $request->input('staff_id') ?? $request->user()->id,
            'job_application_id' => $request->input('job_application_id'),
            'type' => $request->string('type'),
            'mode' => $request->string('mode'),
            'meeting_link' => $request->input('meeting_link'),
            'scheduled_at' => $request->date('scheduled_at'),
            'status' => 'confirmed',
            'notes' => $request->input('notes'),
        ]);

        return back()->with('success', 'Appointment booked and confirmed.');
    }

    public function confirmAppointment(Request $request, User $jobSeeker, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        abort_unless($appointment->student_id === $jobSeeker->id, 404);

        $appointment->update([
            'status' => 'confirmed',
            'staff_id' => $appointment->staff_id ?? $request->user()->id,
        ]);

        return back()->with('success', 'Appointment confirmed.');
    }

    public function cancelAppointment(Request $request, User $jobSeeker, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        abort_unless($appointment->student_id === $jobSeeker->id, 404);

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }

    public function completeAppointment(Request $request, User $jobSeeker, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        abort_unless($appointment->student_id === $jobSeeker->id, 404);

        $appointment->update(['status' => 'completed']);

        return back()->with('success', 'Appointment marked completed.');
    }

    public function updateAppointment(UpdateAppointmentRequest $request, User $jobSeeker, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->student_id === $jobSeeker->id, 404);

        $appointment->update([
            'type' => $request->string('type'),
            'mode' => $request->string('mode'),
            'scheduled_at' => $request->date('scheduled_at'),
            'staff_id' => $request->input('staff_id') ?? $appointment->staff_id,
        ]);

        return back()->with('success', 'Appointment updated.');
    }

    public function storeTicket(StoreSupportTicketRequest $request, User $jobSeeker): RedirectResponse
    {
        $ticket = SupportTicket::create([
            'student_id' => $jobSeeker->id,
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

        return back()->with('success', "Message sent to {$jobSeeker->name}.");
    }

    public function replyTicket(StoreSupportReplyRequest $request, User $jobSeeker, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->student_id === $jobSeeker->id, 404);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('body'),
        ]);

        if (! in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'waiting_for_student']);
        }

        return back()->with('success', 'Reply sent.');
    }

    public function updateTicketStatus(Request $request, User $jobSeeker, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->student_id === $jobSeeker->id, 404);

        $request->validate(['status' => ['required', Rule::in(['open', 'in_progress', 'waiting_for_student', 'resolved', 'closed'])]]);

        $ability = $request->input('status') === 'closed' ? 'close' : 'respond';
        $this->authorize($ability, $ticket);

        $ticket->update(['status' => $request->string('status')]);

        return back()->with('success', 'Ticket status updated.');
    }

    public function storeTask(StoreTaskRequest $request, User $jobSeeker): RedirectResponse
    {
        $profile = $jobSeeker->jobSeekerProfile ?? $jobSeeker->jobSeekerProfile()->create([]);

        Task::create([
            'taskable_type' => JobSeekerProfile::class,
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

    public function completeTask(Request $request, User $jobSeeker, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->update(['status' => 'completed']);

        return back()->with('success', 'Task marked completed.');
    }

    public function storeNote(StoreNoteRequest $request, User $jobSeeker): RedirectResponse
    {
        $profile = $jobSeeker->jobSeekerProfile ?? $jobSeeker->jobSeekerProfile()->create([]);

        Note::create([
            'noteable_type' => JobSeekerProfile::class,
            'noteable_id' => $profile->id,
            'body' => $request->string('body'),
            'is_internal' => $request->boolean('is_internal', true),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Note added.');
    }
}
