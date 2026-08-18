<?php

namespace App\Models;

use App\Concerns\HasExclusiveApplicationLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Document extends Model
{
    use LogsActivity, HasExclusiveApplicationLink;

    protected $fillable = [
        'student_id', 'study_application_id', 'job_application_id', 'job_posting_id', 'document_category_id', 'name',
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

    /**
     * Named student() historically, but this column (student_id → users.id)
     * is the same person-reference used for Job Seekers and Employers too —
     * "student" here is a relationship method name, not a claim about the
     * row owner's role. See jobSeeker() below for the identical relationship
     * under a name that reads naturally in Job Seeker contexts.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Same column as student() — purely a readability alias for code working
     * with Job Seeker documents, so it doesn't read as "->student->name" for
     * a candidate who was never a student.
     */
    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Same column as student()/jobSeeker() — readability alias for Employer
     * document contexts.
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function studyApplication(): BelongsTo
    {
        return $this->belongsTo(StudyApplication::class);
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    /**
     * Tags an EMPLOYER's own document as relating to a specific job posting
     * (e.g. "Employment Contract for the Nurse role") — independent of
     * study_application_id/job_application_id, which describe a CANDIDATE's
     * application-specific documents, not the employer's.
     */
    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
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

    public function scopeForVault($query, int $personId)
    {
        return $query->where('student_id', $personId)->whereNull('study_application_id')->whereNull('job_application_id');
    }

    public function scopeForApplication($query, int $applicationId)
    {
        return $query->where('study_application_id', $applicationId);
    }

    public function scopeForJobApplication($query, int $jobApplicationId)
    {
        return $query->where('job_application_id', $jobApplicationId);
    }
}
