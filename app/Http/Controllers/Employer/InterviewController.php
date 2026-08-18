<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Reuses the shared Appointment table entirely, per the spec's explicit
 * instruction not to build a second scheduling system. Scoped to
 * appointments tied to job_applications for THIS employer's own postings —
 * interviews are scheduled by Admin (see Admin\JobSeeker\ApplicationController
 * ::scheduleInterview), this page is view + join only. Reschedule/Cancel
 * stay deferred, same reasoning as Documents (permission grant doesn't
 * exist yet) — see AppointmentPolicy for where that's enforced.
 */
class InterviewController extends Controller
{
    public function index(Request $request): View
    {
        $jobPostingIds = $request->user()->jobPostings()->pluck('id');

        $appointments = Appointment::whereHas(
            'jobApplication', fn ($q) => $q->whereIn('job_posting_id', $jobPostingIds)
        )
            ->with(['jobApplication.jobPosting', 'jobApplication.jobSeeker', 'staff'])
            ->orderByDesc('scheduled_at')
            ->get();

        $upcoming = $appointments->filter(fn ($a) => $a->scheduled_at->isFuture() && in_array($a->status, ['requested', 'confirmed']))
            ->sortBy('scheduled_at');

        $past = $appointments->filter(fn ($a) => $a->scheduled_at->isPast() || in_array($a->status, ['completed', 'cancelled']));

        return view('employer.interviews.index', [
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }
}
