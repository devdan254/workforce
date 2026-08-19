<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class StudyPosting extends Model
{
    use SoftDeletes;

    protected $table = 'study_postings';

    protected $fillable = [
        'image_path', 'university_name', 'country', 'scholarship_type', 'courses_offered',
        'school_fees_per_semester', 'fees_currency', 'age_requirement', 'requirements',
        'description', 'intake', 'application_eligibility', 'status', 'is_featured',
        'posted_by', 'slug',
    ];

    protected function casts(): array
    {
        return [
            'school_fees_per_semester' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (StudyPosting $posting) {
            if (empty($posting->slug)) {
                $base = str($posting->university_name.'-'.$posting->country)->slug();
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

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(StudyPostingDownload::class);
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
     * Real uploaded banner URL, or null if none exists yet — same pattern
     * as JobPosting::image_url. Views decide what fallback to show instead.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    /**
     * Same "explode a free-text field into a list for display" helper
     * courses_offered needs, since it's stored as one text field (comma or
     * newline separated) rather than a separate courses table.
     */
    public function coursesList(): array
    {
        return collect(preg_split('/[,\n]+/', (string) $this->courses_offered))
            ->map(fn ($course) => trim($course))
            ->filter()
            ->values()
            ->all();
    }
}
