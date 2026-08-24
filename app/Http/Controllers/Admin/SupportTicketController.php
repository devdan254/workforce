<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreSupportMessageRequest;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Reuses SupportTicketPolicy and StoreSupportMessageRequest entirely
 * unmodified — both already had correct staff-branch logic built in
 * (respond()/close() already check tickets.respond/tickets.close for a
 * non-Student/JobSeeker/Employer user) even though no Admin controller
 * existed to use them yet. Student/JobSeeker/Employer submit through
 * three separate controller classes (not one shared, unlike Resources/
 * Notifications), but they all write to the same SupportTicket table —
 * this is the one place all three become visible together.
 */
class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', SupportTicket::class);

        $query = SupportTicket::with(['student', 'assignedTo']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('subject', 'like', "%{$search}%")
                ->orWhere('ticket_number', 'like', "%{$search}%"));
        }
        if ($request->boolean('mine')) {
            $query->where('assigned_to', $request->user()->id);
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();

        return view('admin.support.index', ['tickets' => $tickets]);
    }

    public function show(SupportTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['messages.author', 'assignedTo', 'student']);

        return view('admin.support.show', ['ticket' => $ticket]);
    }

    public function reply(StoreSupportMessageRequest $request, SupportTicket $ticket): RedirectResponse
    {
        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('body'),
        ]);

        // Staff replying moves an open ticket forward — mirrors the
        // Student-side controller's own equivalent nudge in reverse: a
        // reply from support is what "waiting for student" means, so an
        // open/unassigned ticket that gets a real reply should show that
        // it's actually being worked, not sit as "open" indefinitely.
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'waiting_for_student']);
        }

        return back()->with('success', 'Reply sent.');
    }

    public function close(SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('close', $ticket);

        $ticket->update(['status' => 'closed']);

        return back()->with('success', "Ticket {$ticket->ticket_number} closed.");
    }

    public function assignToMe(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('respond', $ticket);

        $ticket->update(['assigned_to' => $request->user()->id]);

        return back()->with('success', 'Ticket assigned to you.');
    }
}
