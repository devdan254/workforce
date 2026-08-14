<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\StatusTransition;
use Illuminate\Database\Seeder;

class StatusesAndTransitionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedApplicationStatuses();
        $this->seedVisaStatuses();
    }

    private function seedApplicationStatuses(): void
    {
        // Order here IS the linear happy-path from the spec's "APPLICATION STATUS ENGINE" diagram.
        $slugs = [
            'application_started',
            'documents_submitted',
            'documents_verified',
            'university_application_submitted',
            'admission_pending',
            'admission_received',
            'admission_accepted',
            'visa_preparation',
            'visa_application',
            'visa_appointment',
            'visa_decision',
            'travel_preparation',
            'travel',
            'completed',
        ];

        $statuses = [];
        foreach ($slugs as $i => $slug) {
            $statuses[$slug] = Status::firstOrCreate(
                ['type' => 'application', 'slug' => $slug],
                [
                    'label' => str($slug)->replace('_', ' ')->title(),
                    'sort_order' => $i + 1,
                    'is_terminal' => $slug === 'completed',
                ]
            );
        }

        // Also allow a terminal "rejected" branch off admission and visa decision stages.
        $rejected = Status::firstOrCreate(
            ['type' => 'application', 'slug' => 'rejected'],
            ['label' => 'Rejected', 'sort_order' => 99, 'is_terminal' => true]
        );

        // Linear happy-path transitions, one step to the next.
        foreach (array_values($statuses) as $i => $status) {
            if (isset(array_values($statuses)[$i + 1])) {
                $this->transition($status, array_values($statuses)[$i + 1], 'application');
            }
        }

        // Branch to Rejected from the two decision points.
        $this->transition($statuses['admission_pending'], $rejected, 'application');
        $this->transition($statuses['visa_decision'], $rejected, 'application');

        // Initial status (from_status_id = null marks a valid starting point).
        StatusTransition::firstOrCreate([
            'from_status_id' => null,
            'to_status_id' => $statuses['application_started']->id,
            'status_type' => 'application',
        ]);
    }

    private function seedVisaStatuses(): void
    {
        $slugs = [
            'documents_submitted',
            'documents_verified',
            'visa_application_prepared',
            'embassy_appointment',
            'visa_decision',
            'travel',
        ];

        $statuses = [];
        foreach ($slugs as $i => $slug) {
            $statuses[$slug] = Status::firstOrCreate(
                ['type' => 'visa', 'slug' => $slug],
                [
                    'label' => str($slug)->replace('_', ' ')->title(),
                    'sort_order' => $i + 1,
                    'is_terminal' => $slug === 'travel',
                ]
            );
        }

        $rejected = Status::firstOrCreate(
            ['type' => 'visa', 'slug' => 'rejected'],
            ['label' => 'Rejected', 'sort_order' => 99, 'is_terminal' => true]
        );

        foreach (array_values($statuses) as $i => $status) {
            if (isset(array_values($statuses)[$i + 1])) {
                $this->transition($status, array_values($statuses)[$i + 1], 'visa');
            }
        }

        $this->transition($statuses['visa_decision'], $rejected, 'visa');

        StatusTransition::firstOrCreate([
            'from_status_id' => null,
            'to_status_id' => $statuses['documents_submitted']->id,
            'status_type' => 'visa',
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
