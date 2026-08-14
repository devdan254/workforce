<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'date_of_birth',
        'gender',
        'nationality',
        'address_line',
        'city',
        'country',
        'highest_qualification',
        'institution',
        'graduation_year',
        'field_of_study',
        'grade',
        'passport_number',
        'passport_issue_date',
        'passport_expiry_date',
        'passport_country',
        'preferred_countries',
        'preferred_course',
        'preferred_study_level',
        'preferred_intake',
        'profile_completion_percent',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'passport_issue_date' => 'date',
            'passport_expiry_date' => 'date',
            'preferred_countries' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Recompute and persist profile completion based on which key fields are filled.
     * Called from an observer whenever the profile is saved.
     */
    public function calculateCompletionPercent(): int
    {
        $trackedFields = [
            'date_of_birth', 'gender', 'nationality', 'address_line', 'city', 'country',
            'highest_qualification', 'institution', 'graduation_year', 'field_of_study',
            'passport_number', 'passport_expiry_date',
            'preferred_countries', 'preferred_course', 'preferred_study_level',
        ];

        $filled = collect($trackedFields)->filter(fn ($field) => ! empty($this->{$field}))->count();

        return (int) round(($filled / count($trackedFields)) * 100);
    }
}
