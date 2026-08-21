<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One reusable Mailable for every "notify admin@alturaworkforce.com when a
 * public form is submitted" requirement — Job Application, Study Abroad
 * Application, Hire/Worker Request, Contact, and now the Visa Application
 * inquiry all send through this same class rather than each getting its
 * own near-identical Mailable. $heading and $lines are the only things
 * that differ per form.
 */
class AdminNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string,string>  $lines  Label => value pairs shown in the email body.
     * @param  array<int,array{content:string,name:string,mime:?string}>  $fileAttachments
     *         Raw bytes, NOT file paths. This class implements ShouldQueue —
     *         the actual send happens later, in a separate queue-worker
     *         process, by which point any uploaded file's temp path
     *         (UploadedFile::getRealPath()) would already be gone; PHP
     *         deletes request-scoped temp uploads once the ORIGINAL request
     *         ends. Callers must read file contents into memory themselves
     *         (e.g. $uploadedFile->get()) before constructing this mail, so
     *         the actual bytes travel with the serialized job instead of a
     *         path reference that goes stale.
     */
    public function __construct(
        public string $heading,
        public array $lines,
        public ?string $actionLabel = null,
        public ?string $actionUrl = null,
        public array $fileAttachments = [],
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

    public function attachments(): array
    {
        return collect($this->fileAttachments)
            ->map(fn ($file) => Attachment::fromData(fn () => $file['content'], $file['name'])
                ->withMime($file['mime'] ?? 'application/octet-stream'))
            ->all();
    }
}
