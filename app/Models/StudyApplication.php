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

class StudyApplication extends Model implements HasStatusWorkflow
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'student_id', 'university_id', 'course_id', 'reference_number',
        'status_id', 'intake', 'application_deadline', 'assigned_officer_id',
        'application_fee', 'tuition_fee', 'service_fee', 'currency', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'application_deadline' => 'date',
            'submitted_at' => 'datetime',
            'application_fee' => 'decimal:2',
            'tuition_fee' => 'decimal:2',
            'service_fee' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status_id', 'assigned_officer_id'])
            ->logOnlyDirty();
    }

    protected static function booted(): void
    {
        static::creating(function (StudyApplication $application) {
            if (empty($application->reference_number)) {
                $application->reference_number = 'ALT-'.now()->format('Y').'-'.str_pad(
                    (string) (static::withTrashed()->count() + 1),
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    /* ---------- Relationships ---------- */

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
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

    public function admission(): HasOne
    {
        return $this->hasOne(Admission::class);
    }

    public function visaApplication(): HasOne
    {
        return $this->hasOne(VisaApplication::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
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
        return 'application';
    }
}
