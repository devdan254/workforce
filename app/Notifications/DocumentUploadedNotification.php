<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class DocumentUploadedNotification extends Notification
{
    use Queueable;

    public function __construct(public Document $document) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Document Uploaded',
            'body' => "{$this->document->student->name} uploaded \"{$this->document->name}\" — awaiting your review.",
            'link' => Route::has('admin.students.documents')
                ? route('admin.students.documents', $this->document->student_id)
                : '#',
            'icon' => 'file-arrow-up',
        ];
    }
}
