<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        if ($user->isStudent()) {
            return $payment->student_id === $user->id;
        }

        return $user->can('payments.view');
    }

    public function create(User $user): bool
    {
        // A student recording that they've paid (e.g. submitting an M-Pesa code) is a "create";
        // staff can also record a payment on a student's behalf.
        return $user->isStudent() || $user->can('payments.create');
    }

    public function confirm(User $user, Payment $payment): bool
    {
        return $user->can('payments.confirm') && ! $user->isStudent();
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->can('payments.refund') && ! $user->isStudent();
    }
}
