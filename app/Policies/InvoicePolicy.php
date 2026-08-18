<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->isStudent() || $user->isJobSeeker() || $user->isEmployer()) {
            return $invoice->student_id === $user->id;
        }

        return $user->can('invoices.view');
    }

    public function create(User $user): bool
    {
        return $user->can('invoices.create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can('invoices.update') && $invoice->status !== 'paid';
    }

    public function send(User $user, Invoice $invoice): bool
    {
        return $user->can('invoices.send');
    }

    public function cancel(User $user, Invoice $invoice): bool
    {
        return $user->can('invoices.update') && $invoice->status !== 'paid';
    }

    public function pay(User $user, Invoice $invoice): bool
    {
        // Only the owning student, job seeker, or employer initiates payment.
        return ($user->isStudent() || $user->isJobSeeker() || $user->isEmployer()) && $invoice->student_id === $user->id;
    }
}
