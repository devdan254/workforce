<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobExperience extends Model
{
     protected $table = 'job_experiences';
    protected $fillable = ['job_seeker_profile_id', 'occupation', 'employer', 'years_of_experience', 'is_current', 'sort_order'];

    protected function casts(): array
    {
        return [
            'years_of_experience' => 'decimal:1',
            'is_current' => 'boolean',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(JobSeekerProfile::class, 'job_seeker_profile_id');
    }
}
