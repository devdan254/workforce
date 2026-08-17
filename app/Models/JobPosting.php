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
