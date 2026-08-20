<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class JobPosting extends Model
{
    use SoftDeletes;

    protected $table = 'job_postings';

    protected $fillable = [
        'job_category_id', 'employer_id', 'title', 'image_path', 'country', 'city',
        'currency', 'salary_min', 'salary_max', 'vacancies', 'employment_type',
        'experience_required', 'education_requirement',
        'description', 'responsibilities', 'requirements', 'skills', 'languages', 'benefits',
        'accommodation_provided', 'meals_provided', 'visa_support_provided', 'air_ticket_provided',
        'working_hours', 'application_deadline', 'status', 'is_featured', 'posted_by', 'slug',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'accommodation_provided' => 'boolean',
            'meals_provided' => 'boolean',
            'visa_support_provided' => 'boolean',
            'air_ticket_provided' => 'boolean',
            'is_featured' => 'boolean',
            'application_deadline' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (JobPosting $posting) {
            if (empty($posting->slug)) {
                $base = str($posting->title.'-'.$posting->country)->slug();
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }
                $posting->slug = $slug;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * The employer this posting originated from, if any — nullable by
     * design (Admin-sourced postings have no employer at all). This was
     * the missing half of User::jobPostings(); the inverse existed since
     * Stage 3 Step 1, but nothing had actually added the forward
     * relationship until this fix, so $posting->employer always silently
     * returned null even when employer_id was correctly set.
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Real uploaded photo URL, or null if none exists yet — views decide
     * what to show instead (see iconForCategory()/colorForCategory() below).
     * Admin/Employer photo upload lands here in a later Stage 2/3 delivery;
     * this accessor already supports it without any further model changes.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    /**
     * responsibilities/requirements/skills/benefits are all stored as free
     * text (one item per line, or comma-separated) rather than separate
     * tables — same reasoning as StudyPosting::coursesList(). One shared
     * splitter, four named accessors so the public Job Details view stays
     * readable rather than calling a generic helper with a field name string.
     */
    private function splitLines(?string $text): array
    {
        return collect(preg_split('/[,\n]+/', (string) $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    public function responsibilitiesList(): array
    {
        return $this->splitLines($this->responsibilities);
    }

    public function requirementsList(): array
    {
        return $this->splitLines($this->requirements);
    }

    public function skillsList(): array
    {
        return $this->splitLines($this->skills);
    }

    public function benefitsList(): array
    {
        return $this->splitLines($this->benefits);
    }

    /**
     * Copyright-safe visual fallback for postings without a real photo yet —
     * a category-themed icon treatment, not a hotlinked stock image of
     * uncertain license. Every seeded demo posting uses this today.
     */
    public function iconForCategory(): string
    {
        return match ($this->category?->name) {
            'Healthcare' => 'fa-user-nurse',
            'Construction' => 'fa-helmet-safety',
            'Hospitality' => 'fa-bed',
            'Domestic Work' => 'fa-house',
            'Security' => 'fa-shield-halved',
            'Logistics & Driving' => 'fa-truck',
            default => 'fa-briefcase',
        };
    }

    public function colorForCategory(): string
    {
        return match ($this->category?->name) {
            'Healthcare' => '#1774B8',
            'Construction' => '#C77700',
            'Hospitality' => '#8B5CF6',
            'Domestic Work' => '#1E8E5A',
            'Security' => '#082159',
            'Logistics & Driving' => '#0EA5A5',
            default => '#47516E',
        };
    }
}
