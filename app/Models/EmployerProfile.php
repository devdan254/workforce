<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EmployerProfile extends Model
{
    protected $table = 'employer_profiles';

    protected $fillable = [
        'user_id', 'assigned_officer_id', 'company_name', 'industry', 'country', 'company_website',
        'company_size', 'city', 'company_description', 'logo_path',
        'contact_job_title', 'profile_completion_percent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * One assigned Account Manager per company — account-level, not
     * per-application, since Employer has no per-submission relationship
     * with Altura the way Student/Job Seeker applications do.
     */
    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    /**
     * Same pattern as JobPosting::image_url — keeps Storage:: calls out of
     * Blade views entirely (a bare Storage:: reference there fails without
     * a namespace import, which Blade views don't have by default).
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function calculateCompletionPercent(): int
    {
        $trackedFields = [
            'company_name', 'industry', 'country', 'company_website', 'company_size',
            'city', 'company_description', 'contact_job_title',
        ];

        $filled = collect($trackedFields)->filter(fn ($field) => ! empty($this->{$field}))->count();

        return (int) round(($filled / count($trackedFields)) * 100);
    }
}
