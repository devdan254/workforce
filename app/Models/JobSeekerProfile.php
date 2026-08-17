<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobSeekerProfile extends Model
{
    protected $table = 'job_seeker_profiles';
    protected $fillable = [
        'user_id', 'date_of_birth', 'gender', 'nationality',
        'address_line', 'city', 'country',
        'professional_title', 'industry', 'skills', 'languages', 'certifications',
        'passport_number', 'passport_issue_date', 'passport_expiry_date', 'passport_country',
        'preferred_countries', 'preferred_industries', 'preferred_positions', 'preferred_employment_type',
        'expected_salary', 'expected_salary_currency', 'earliest_availability',
        'willing_to_relocate', 'worked_abroad_before', 'profile_completion_percent',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'passport_issue_date' => 'date',
            'passport_expiry_date' => 'date',
            'earliest_availability' => 'date',
            'preferred_countries' => 'array',
            'preferred_industries' => 'array',
            'preferred_positions' => 'array',
            'willing_to_relocate' => 'boolean',
            'worked_abroad_before' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(JobExperience::class)->orderBy('sort_order');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(JobEducation::class)->orderBy('sort_order');
    }

    /**
     * Computed, not stored — "highest qualification" is derived from the
     * education records rather than duplicated as its own column, so there's
     * nothing to keep in sync when an education entry is added/edited/removed.
     */
    public function highestQualification(): ?JobEducation
    {
        $rank = ['not_applicable' => 0, 'primary' => 1, 'secondary' => 2, 'diploma' => 3, 'bachelor' => 4, 'master' => 5];

        return $this->educations->sortByDesc(fn ($e) => $rank[$e->level] ?? 0)->first();
    }

    public function calculateCompletionPercent(): int
    {
        $trackedFields = [
            'date_of_birth', 'gender', 'nationality', 'address_line', 'city', 'country',
            'professional_title', 'industry', 'skills',
            'passport_number', 'passport_expiry_date',
            'preferred_countries', 'preferred_industries', 'expected_salary', 'earliest_availability',
        ];

        $filled = collect($trackedFields)->filter(fn ($field) => ! empty($this->{$field}))->count();
        $hasExperience = $this->experiences()->exists() ? 1 : 0;
        $hasEducation = $this->educations()->exists() ? 1 : 0;

        return (int) round((($filled + $hasExperience + $hasEducation) / (count($trackedFields) + 2)) * 100);
    }
}
