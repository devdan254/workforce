<?php

namespace App\Models;

use App\Contracts\HasStatusWorkflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Mirrors StudyApplication's shape deliberately — same relationship patterns,
 * same HasStatusWorkflow implementation, same reference-number generation.
 * This is the proof that the Stage 1 architecture's future-proofing worked:
 * ApplicationStatusService, status_histories, tasks, notes all attach here
 * completely unmodified.
 */
class JobApplication extends Model implements HasStatusWorkflow
{
    use SoftDeletes, LogsActivity;

    protected $table = 'job_applications';

    protected $fillable = [
        'job_seeker_id', 'job_posting_id', 'reference_number',
        'status_id', 'assigned_officer_id', 'applied_at',
    ];

    protected function casts(): array
    {
        return ['applied_at' => 'datetime'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status_id', 'assigned_officer_id'])
            ->logOnlyDirty();
    }

    protected static function booted(): void
    {
        static::creating(function (JobApplication $application) {
            if (empty($application->reference_number)) {
                $application->reference_number = 'ALT-JA-'.now()->format('Y').'-'.str_pad(
                    (string) (static::withTrashed()->count() + 1),
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    /* ---------- Relationships ---------- */

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'job_seeker_id');
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'historyable')->latest();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function offer(): HasOne
    {
        return $this->hasOne(JobOffer::class);
    }

    public function visaApplication(): HasOne
    {
        return $this->hasOne(VisaApplication::class);
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function statusType(): string
    {
        return 'job_application';
    }
}