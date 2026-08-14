<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'university_id', 'name', 'study_level', 'duration_months',
        'tuition_fee', 'currency', 'intake_months', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'intake_months' => 'array',
            'is_active' => 'boolean',
            'tuition_fee' => 'decimal:2',
        ];
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function studyApplications(): HasMany
    {
        return $this->hasMany(StudyApplication::class);
    }
}
