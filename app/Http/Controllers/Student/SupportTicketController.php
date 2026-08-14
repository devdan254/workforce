<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreSupportMessageRequest;
use App\Http\Requests\Student\StoreSupportTicketRequest;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', SupportTicket::class);

        $tickets = $request->user()->supportTickets()->latest()->paginate(10);

        return view('student.support.index', ['tickets' => $tickets]);
    }

    public function create(): View
    {
        $this->authorize('create', SupportTicket::class);

        return view('student.support.create');
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

        return redirect()
            ->route('student.support.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_number} created — our support team will respond shortly.");
    }

    public function show(SupportTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['messages.author', 'assignedTo']);

        return view('student.support.show', ['ticket' => $ticket]);
    }

    public function reply(StoreSupportMessageRequest $request, SupportTicket $ticket): RedirectResponse
    {
        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('body'),
        ]);

        // A student replying to a resolved/waiting ticket re-opens the conversation —
        // otherwise it would silently sit as "resolved" while they're still talking.
        if (in_array($ticket->status, ['resolved', 'waiting_for_student'])) {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Message sent.');
    }
}
