<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JobOffer extends Model
{
    use LogsActivity;
    protected $table = 'job_offers';
    protected $fillable = [
        'job_application_id', 'salary', 'currency', 'benefits', 'working_hours',
        'accommodation_provided', 'meals_provided', 'air_ticket_provided',
        'contract_duration', 'start_date', 'location',
        'status', 'offer_letter_document_id', 'sent_at', 'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'accommodation_provided' => 'boolean',
            'meals_provided' => 'boolean',
            'air_ticket_provided' => 'boolean',
            'start_date' => 'date',
            'sent_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status'])->logOnlyDirty();
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function offerLetterDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'offer_letter_document_id');
    }
}
