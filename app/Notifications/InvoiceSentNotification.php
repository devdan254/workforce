<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class InvoiceSentNotification extends Notification
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Invoice Generated',
            'body' => "A new invoice ({$this->invoice->invoice_number}) for {$this->invoice->currency} ".number_format((float) $this->invoice->total, 2).' has been generated.',
            'link' => Route::has('student.invoices.show') ? route('student.invoices.show', $this->invoice) : '#',
            'icon' => 'file-invoice',
        ];
    }
}
