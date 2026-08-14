<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model
{
    protected $fillable = [
        'study_application_id', 'decision', 'offer_letter_document_id', 'conditions', 'decided_at',
    ];

    protected function casts(): array
    {
        return ['decided_at' => 'datetime'];
    }

    public function studyApplication(): BelongsTo
    {
        return $this->belongsTo(StudyApplication::class);
    }

    public function offerLetterDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'offer_letter_document_id');
    }
}
