<?php

namespace App\Services;

use App\Contracts\HasStatusWorkflow;
use App\Events\StatusChanged;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\StatusHistory;
use App\Models\StatusTransition;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Drives status transitions for ANY model implementing HasStatusWorkflow
 * (currently StudyApplication and VisaApplication). This is the ONLY
 * place status_id should ever be written — controllers must never do
 * $application->update(['status_id' => ...]) directly, or the transition
 * graph and audit trail are silently bypassed.
 */
class ApplicationStatusService
{
    /**
     * @throws InvalidStatusTransitionException
     */
    public function transition(
        Model&HasStatusWorkflow $statusable,
        int $toStatusId,
        User $actor,
        ?string $note = null,
    ): StatusHistory {
        return DB::transaction(function () use ($statusable, $toStatusId, $actor, $note) {
            $fromStatusId = $statusable->status_id;
            $type = $statusable->statusType();

            if (! $this->isTransitionAllowed($type, $fromStatusId, $toStatusId)) {
                throw InvalidStatusTransitionException::make($type, $fromStatusId, $toStatusId);
            }

            $statusable->update(['status_id' => $toStatusId]);

            /** @var StatusHistory $history */
            $history = $statusable->statusHistories()->create([
                'from_status_id' => $fromStatusId,
                'to_status_id' => $toStatusId,
                'changed_by' => $actor->id,
                'note' => $note,
            ]);

            event(new StatusChanged($statusable, $history));

            return $history;
        });
    }

    /**
     * What statuses can this record legally move to next, right now?
     * Used by the Admin UI to render only valid options — never a free-text status field.
     */
    public function allowedNextStatuses(Model&HasStatusWorkflow $statusable)
    {
        return StatusTransition::query()
            ->where('status_type', $statusable->statusType())
            ->where(function ($query) use ($statusable) {
                $statusable->status_id
                    ? $query->where('from_status_id', $statusable->status_id)
                    : $query->whereNull('from_status_id');
            })
            ->with('toStatus')
            ->get()
            ->pluck('toStatus')
            ->sortBy('sort_order')
            ->values();
    }

    protected function isTransitionAllowed(string $type, ?int $fromStatusId, int $toStatusId): bool
    {
        return StatusTransition::query()
            ->where('status_type', $type)
            ->where('to_status_id', $toStatusId)
            ->where(function ($query) use ($fromStatusId) {
                $fromStatusId
                    ? $query->where('from_status_id', $fromStatusId)
                    : $query->whereNull('from_status_id');
            })
            ->exists();
    }
}
