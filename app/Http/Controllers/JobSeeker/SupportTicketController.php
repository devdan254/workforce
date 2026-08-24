<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobSeeker\StoreSupportTicketRequest;
use App\Models\SupportTicket;
use App\Notifications\AdminAlertNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', SupportTicket::class);

        $tickets = $request->user()->supportTickets()->latest()->paginate(10);

        return view('job-seeker.support.index', ['tickets' => $tickets]);
    }

    public function create(): View
    {
        $this->authorize('create', SupportTicket::class);

        return view('job-seeker.support.create');
    }

    public function store(StoreSupportTicketRequest $request): RedirectResponse
    {
        $ticket = SupportTicket::create([
            'student_id' => $request->user()->id,
            'category' => $request->string('category'),
            'priority' => $request->string('priority'),
            'subject' => $request->string('subject'),
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('message'),
        ]);

        AdminAlertNotification::sendToAdmins(
            heading: 'New Support Ticket — Job Seeker',
            lines: [
                'Job Seeker' => $request->user()->name,
                'Ticket' => $ticket->ticket_number,
                'Subject' => $ticket->subject,
                'Priority' => ucfirst($ticket->priority),
            ],
            actionLabel: 'View Ticket',
            actionUrl: route('admin.support.show', $ticket),
        );

        return redirect()
            ->route('job-seeker.support.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_number} created — our support team will respond shortly.");
    }

    public function show(SupportTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['messages.author', 'assignedTo']);

        return view('job-seeker.support.show', ['ticket' => $ticket]);
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('respond', $ticket);

        $request->validate(['body' => ['required', 'string', 'max:5000']]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('body'),
        ]);

        if (in_array($ticket->status, ['resolved', 'waiting_for_student'])) {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Message sent.');
    }
}
