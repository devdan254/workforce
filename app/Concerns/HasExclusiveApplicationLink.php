<?php

namespace App\Concerns;

use InvalidArgumentException;

/**
 * Enforces that a record belongs to AT MOST ONE of study_application_id /
 * job_application_id — never both. A record can be:
 *   - Study-application-scoped (study_application_id set, job_application_id null)
 *   - Job-application-scoped (job_application_id set, study_application_id null)
 *   - User-level / vault-level (both null)
 * but never both simultaneously.
 *
 * Enforced at the MODEL level (via the `saving` Eloquent event) rather than
 * scattered across Form Requests — this guarantees it holds for every current
 * AND future write path (seeders, services, admin actions, anything), not
 * just the controllers that happen to validate it today. A DB-level CHECK
 * constraint was deliberately NOT added alongside this: CHECK constraints are
 * silently unenforced on MySQL/MariaDB versions before 8.0.16, which would
 * give false confidence depending on the deployment target. This model-level
 * guard works identically regardless of DB version.
 */
trait HasExclusiveApplicationLink
{
    public static function bootHasExclusiveApplicationLink(): void
    {
        static::saving(function ($model) {
            if (! is_null($model->study_application_id) && ! is_null($model->job_application_id)) {
                throw new InvalidArgumentException(
                    class_basename($model).' cannot be linked to both a Study Application and a Job Application simultaneously (id: '.($model->id ?? 'new').').'
                );
            }
        });
    }
}
