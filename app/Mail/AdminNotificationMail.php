<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One reusable Mailable for every "notify admin@alturaworkforce.com when a
 * public form is submitted" requirement — Job Application, Study Abroad
 * Application, Hire/Worker Request, and Contact all send through this same
 * class rather than each getting its own near-identical Mailable. $subject
 * and $lines are the only things that differ per form.
 */
class AdminNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string,string>  $lines  Label => value pairs shown in the email body.
     */
    public function __construct(
        public string $heading,
        public array $lines,
        public ?string $actionLabel = null,
        public ?string $actionUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->heading,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-notification',
        );
    }
}
