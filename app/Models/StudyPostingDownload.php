<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StudyPostingDownload extends Model
{
    protected $fillable = ['study_posting_id', 'title', 'file_path'];

    public function studyPosting(): BelongsTo
    {
        return $this->belongsTo(StudyPosting::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }
}
