<?php

namespace App\Contracts;

/**
 * Implemented by any model that has a database-driven status workflow
 * (currently StudyApplication and VisaApplication). Lets ApplicationStatusService
 * remain generic instead of hardcoding model checks — and lets Stage 2/3
 * (e.g. a future recruitment pipeline "candidate status") reuse the same
 * service without touching it.
 */
interface HasStatusWorkflow
{
    /**
     * Which row of `statuses`/`status_transitions` this model's workflow belongs to.
     * e.g. 'application', 'visa'.
     */
    public function statusType(): string;
}
