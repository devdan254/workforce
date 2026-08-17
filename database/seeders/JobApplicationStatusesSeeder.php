<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\StatusTransition;
use Illuminate\Database\Seeder;

/**
 * Seeds the job_application status workflow — same statuses/status_transitions
 * tables StatusesAndTransitionsSeeder already populated for 'application' and
 * 'visa' types, just a new `type` value. ApplicationStatusService needs zero
 * changes to drive this — it was built generic from day one.
 *
 * Models the spec's two paths (Section 29) as a branch, not two separate
 * workflows: Shortlisted can go straight to Interview Scheduled (Altura-only)
 * OR to Employer Review first (Altura + Employer) — both are valid next steps
 * from the same status, exactly like Rejected already branches off multiple
 * points in the Student application workflow.
 */
class JobApplicationStatusesSeeder extends Seeder
{
    public function run(): void
    {
        $slugs = [
            'application_submitted',
            'under_review',
            'documents_required',
            'documents_verified',
            'shortlisted',
            'employer_review',       // optional branch — see below
            'interview_scheduled',
            'interview_completed',
            'selected',
            'offer_extended',
            'offer_accepted',
            'documentation',
            'visa_processing',
            'travel_preparation',
            'deployment_ready',
            'deployed',
        ];

        $statuses = [];
        foreach ($slugs as $i => $slug) {
            $statuses[$slug] = Status::firstOrCreate(
                ['type' => 'job_application', 'slug' => $slug],
                [
                    'label' => str($slug)->replace('_', ' ')->title(),
                    'sort_order' => $i + 1,
                    'is_terminal' => $slug === 'deployed',
                ]
            );
        }

        $rejected = Status::firstOrCreate(
            ['type' => 'job_application', 'slug' => 'rejected'],
            ['label' => 'Rejected', 'sort_order' => 98, 'is_terminal' => true]
        );
        $withdrawn = Status::firstOrCreate(
            ['type' => 'job_application', 'slug' => 'withdrawn'],
            ['label' => 'Withdrawn', 'sort_order' => 99, 'is_terminal' => true]
        );

        // Linear happy-path, one step to the next...
        $ordered = array_values($statuses);
        foreach ($ordered as $i => $status) {
            if (isset($ordered[$i + 1])) {
                // ...EXCEPT Shortlisted, handled separately below as a branch.
                if ($status->slug !== 'shortlisted') {
                    $this->transition($status, $ordered[$i + 1], 'job_application');
                }
            }
        }

        // The Altura-only vs Altura+Employer branch: Shortlisted can go
        // directly to Interview Scheduled, OR to Employer Review first.
        $this->transition($statuses['shortlisted'], $statuses['interview_scheduled'], 'job_application');
        $this->transition($statuses['shortlisted'], $statuses['employer_review'], 'job_application');
        // From Employer Review, the (assumed-positive) next step is Interview.
        $this->transition($statuses['employer_review'], $statuses['interview_scheduled'], 'job_application');

        // Rejected branches off the points where a candidate can genuinely be turned down.
        foreach (['under_review', 'documents_verified', 'shortlisted', 'employer_review', 'interview_completed', 'offer_extended'] as $slug) {
            $this->transition($statuses[$slug], $rejected, 'job_application');
        }

        // Withdrawn (candidate-initiated) can happen from any non-terminal, pre-offer-acceptance point.
        foreach (['application_submitted', 'under_review', 'documents_required', 'documents_verified', 'shortlisted', 'employer_review', 'interview_scheduled', 'interview_completed', 'offer_extended'] as $slug) {
            $this->transition($statuses[$slug], $withdrawn, 'job_application');
        }

        // Initial status.
        StatusTransition::firstOrCreate([
            'from_status_id' => null,
            'to_status_id' => $statuses['application_submitted']->id,
            'status_type' => 'job_application',
        ]);
    }

    private function transition(Status $from, Status $to, string $type): void
    {
        StatusTransition::firstOrCreate([
            'from_status_id' => $from->id,
            'to_status_id' => $to->id,
            'status_type' => $type,
        ]);
    }
}
