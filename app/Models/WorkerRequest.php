<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerRequest extends Model
{
    protected $table = 'worker_requests';

    protected $fillable = [
        'employer_id', 'contact_name', 'contact_job_title', 'contact_email', 'contact_phone',
        'job_title', 'quantity', 'preferred_experience', 'employment_type', 'age_range',
        'preferred_start_date', 'work_location',
        'salary_min', 'salary_max', 'currency', 'responsibilities', 'skills', 'benefits', 'requirements',
        'additional_requirements',
        'status', 'reviewed_by', 'reviewed_at', 'review_notes',
        'employer_response', 'employer_responded_at',
        'job_posting_id',
    ];

    protected function casts(): array
    {
        return [
            'preferred_start_date' => 'date',
            'reviewed_at' => 'datetime',
            'employer_responded_at' => 'datetime',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }
}
