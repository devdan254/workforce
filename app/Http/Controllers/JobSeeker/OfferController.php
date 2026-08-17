<?php

namespace App\Http\Controllers\JobSeeker;

use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use App\Models\Status;
use App\Models\SupportTicket;
use App\Services\ApplicationStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OfferController extends Controller
{
    public function index(Request $request): View
    {
        $offers = JobOffer::whereHas('jobApplication', fn ($q) => $q->where('job_seeker_id', $request->user()->id))
            ->with(['jobApplication.jobPosting'])
            ->latest()
            ->paginate(10);

        return view('job-seeker.offers.index', ['offers' => $offers]);
    }

    public function show(JobOffer $offer): View
    {
        $this->authorize('view', $offer);

        $offer->load(['jobApplication.jobPosting.category', 'offerLetterDocument']);

        return view('job-seeker.offers.show', ['offer' => $offer]);
    }

    /**
     * "Offer acceptance must update the application workflow" — per the
     * spec, this is not just flipping the offer's own status. It also
     * transitions the parent JobApplication forward via the same
     * ApplicationStatusService every other status change in this app uses,
     * so the Application Details timeline reflects it too, not just the
     * offer record in isolation.
     */
    public function accept(Request $request, JobOffer $offer, ApplicationStatusService $statusService): RedirectResponse
    {
        $this->authorize('decide', $offer);

        $offer->update(['status' => 'accepted', 'decided_at' => now()]);

        $application = $offer->jobApplication;
        $acceptedStatus = Status::where('type', 'job_application')->where('slug', 'offer_accepted')->first();

        if ($acceptedStatus) {
            try {
                $statusService->transition($application, $acceptedStatus->id, $request->user(), 'Offer accepted by candidate.');
            } catch (InvalidStatusTransitionException $e) {
                // Offer status still updates even if the application was already past
                // this point somehow — the offer decision itself is never blocked.
            }
        }

        return redirect()->route('job-seeker.offers.show', $offer)->with('success', 'Offer accepted! Our team will be in touch about next steps.');
    }

    public function decline(Request $request, JobOffer $offer, ApplicationStatusService $statusService): RedirectResponse
    {
        $this->authorize('decide', $offer);

        $offer->update(['status' => 'declined', 'decided_at' => now()]);

        $application = $offer->jobApplication;
        $withdrawnStatus = Status::where('type', 'job_application')->where('slug', 'withdrawn')->first();

        if ($withdrawnStatus) {
            try {
                $statusService->transition($application, $withdrawnStatus->id, $request->user(), 'Offer declined by candidate.');
            } catch (InvalidStatusTransitionException $e) {
                // Same reasoning as accept() — the decision itself always goes through.
            }
        }

        return redirect()->route('job-seeker.offers.index')->with('info', 'Offer declined.');
    }

    /**
     * Reuses the existing Support Ticket infrastructure directly — a
     * clarification request IS a support ticket, not a new concept, matching
     * the spec's reuse mandate. Works even though the full Support page
     * isn't built yet, since SupportTicket/SupportTicketPolicy already fully
     * support Job Seekers (Stage 2 Step 3's Policy updates).
     */
    public function requestClarification(Request $request, JobOffer $offer): RedirectResponse
    {
        $this->authorize('view', $offer);

        $request->validate(['message' => ['required', 'string', 'max:2000']]);

        $ticket = SupportTicket::create([
            'student_id' => $request->user()->id,
            'category' => 'Job Offer',
            'priority' => 'medium',
            'subject' => 'Clarification on offer: '.$offer->jobApplication->jobPosting->title,
            'status' => 'open',
        ]);
        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('message'),
        ]);

        return back()->with('success', "Question sent — ticket {$ticket->ticket_number} created. Our team will respond shortly.");
    }

    public function downloadLetter(JobOffer $offer): StreamedResponse
    {
        $this->authorize('view', $offer);

        abort_unless($offer->offerLetterDocument && $offer->offerLetterDocument->file_path, 404, 'Offer letter not available yet.');

        return Storage::disk('public')->download($offer->offerLetterDocument->file_path, 'Offer Letter.pdf');
    }
}
