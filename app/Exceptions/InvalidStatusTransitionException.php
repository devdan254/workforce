<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidStatusTransitionException extends RuntimeException
{
    public static function make(string $statusType, ?int $fromStatusId, int $toStatusId): self
    {
        return new self(
            "Illegal {$statusType} status transition: from status ID "
            .($fromStatusId ?? 'NULL')." to status ID {$toStatusId}. "
            .'This transition does not exist in status_transitions — add it there if it should be allowed.'
        );
    }
}
