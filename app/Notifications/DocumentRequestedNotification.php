<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class DocumentRequestedNotification extends Notification
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
            'title' => 'Document Required',
            'body' => "Please upload your {$this->document->name}.",
            'link' => Route::has('student.documents.index') ? route('student.documents.index') : '#',
            'icon' => 'file-circle-exclamation',
        ];
    }
}
