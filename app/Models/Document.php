<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Document extends Model
{
    use LogsActivity;

    protected $fillable = [
        'student_id', 'study_application_id', 'document_category_id', 'name',
        'file_path', 'mime_type', 'size_bytes', 'status', 'uploaded_at',
        'verified_at', 'verified_by', 'rejection_reason', 'verification_notes',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'verified_by', 'rejection_reason'])
            ->logOnlyDirty();
    }

    /* ---------- Relationships ---------- */

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function studyApplication(): BelongsTo
    {
        return $this->belongsTo(StudyApplication::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /* ---------- Query scopes ---------- */

    public function scopeForVault($query, int $studentId)
    {
        return $query->where('student_id', $studentId)->whereNull('study_application_id');
    }

    public function scopeForApplication($query, int $applicationId)
    {
        return $query->where('study_application_id', $applicationId);
    }
}
