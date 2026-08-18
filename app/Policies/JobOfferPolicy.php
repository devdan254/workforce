<?php

namespace App\Policies;

use App\Models\JobOffer;
use App\Models\User;

/**
 * JobOffer has no direct job_seeker_id OR employer_id column — ownership
 * is checked transitively through jobApplication for the candidate, and
 * through jobApplication.jobPosting.employer_id for the employer, same
 * pattern as checking a Payment's owner through its parent Invoice.
 */
class JobOfferPolicy
{
    public function view(User $user, JobOffer $offer): bool
    {
        if ($user->isJobSeeker()) {
            return $offer->jobApplication->job_seeker_id === $user->id;
        }

        if ($user->isEmployer()) {
            return $offer->jobApplication->jobPosting->employer_id === $user->id;
        }

        return $user->can('job_applications.view');
    }

    /**
     * Accept/Decline — the candidate's own decision, only while the offer
     * is still pending. Staff never accept/decline on a candidate's behalf;
     * that would misrepresent the candidate's actual choice.
     */
    public function decide(User $user, JobOffer $offer): bool
    {
        return $user->isJobSeeker()
            && $offer->jobApplication->job_seeker_id === $user->id
            && $offer->status === 'pending';
    }
}
