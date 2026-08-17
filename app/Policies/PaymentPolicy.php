<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        if ($user->isStudent() || $user->isJobSeeker()) {
            return $payment->student_id === $user->id;
        }

        return $user->can('payments.view');
    }

    public function create(User $user): bool
    {
        // A student/job seeker recording that they've paid (e.g. submitting an M-Pesa code)
        // is a "create"; staff can also record a payment on their behalf.
        return $user->isStudent() || $user->isJobSeeker() || $user->can('payments.create');
    }

    /**
     * Editing amount/method — only while still pending. Once confirmed or
     * refunded, the record is part of the financial trail; changing it after
     * the fact is exactly the kind of silent-drift the spec's "one canonical
     * record" rule exists to prevent. Fix it via refund + new payment instead.
     */
    public function update(User $user, Payment $payment): bool
    {
        return $user->can('payments.update') && $user->isStaff() && $payment->status === 'pending';
    }

    public function confirm(User $user, Payment $payment): bool
    {
        return $user->can('payments.confirm') && $user->isStaff();
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->can('payments.refund') && $user->isStaff();
    }
}
