<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Resource extends Model
{
    protected $fillable = ['title', 'category', 'audience', 'type', 'body', 'file_path', 'is_published', 'created_by'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'audience' => 'array',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Empty/null audience means "visible to everyone" — the exact
     * behavior every resource had before this column existed, kept as
     * the default so nothing already seeded silently disappears. A
     * resource targeting one or more specific audiences only matches
     * when $role is actually one of them.
     */
    public function scopeForAudience($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->whereNull('audience')
                ->orWhereJsonLength('audience', 0)
                ->orWhereJsonContains('audience', $role);
        });
    }

    /**
     * Human-readable "Everyone / Students / Job Seekers / Employers" for
     * Admin's own listing — never displayed to Student/Job Seeker/Employer
     * portals themselves, they only ever see resources actually meant for them.
     */
    public function audienceLabel(): string
    {
        $audience = $this->audience ?? [];

        if (empty($audience)) {
            return 'Everyone';
        }

        $labels = ['student' => 'Students', 'job_seeker' => 'Job Seekers', 'employer' => 'Employers'];

        return collect($audience)->map(fn ($role) => $labels[$role] ?? $role)->implode(', ');
    }
}
