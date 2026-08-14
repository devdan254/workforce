<?php

namespace App\Models;

use App\Contracts\HasStatusWorkflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VisaApplication extends Model implements HasStatusWorkflow
{
    use LogsActivity;

    protected $fillable = [
        'study_application_id', 'destination_country', 'status_id',
        'embassy_appointment_at', 'decision', 'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'embassy_appointment_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status_id', 'decision'])->logOnlyDirty();
    }

    public function studyApplication(): BelongsTo
    {
        return $this->belongsTo(StudyApplication::class);
    }

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'historyable')->latest();
    }

    public function statusType(): string
    {
        return 'visa';
    }
}
