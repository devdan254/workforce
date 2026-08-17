<?php

namespace App\Models;

use App\Concerns\HasExclusiveApplicationLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasExclusiveApplicationLink;

    protected $fillable = [
        'student_id', 'staff_id', 'study_application_id', 'job_application_id',
        'type', 'mode', 'meeting_link', 'scheduled_at', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Same column as student() — readability alias for Job Seeker contexts
     * (e.g. interviews, which reuse this table per the Stage 2 spec).
     */
    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function studyApplication(): BelongsTo
    {
        return $this->belongsTo(StudyApplication::class);
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }
}