<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobEducation extends Model
{
     protected $table = 'job_educations';
    protected $fillable = ['job_seeker_profile_id', 'level', 'institution', 'course', 'graduation_year', 'sort_order'];
    

    public function profile(): BelongsTo
    {
        return $this->belongsTo(JobSeekerProfile::class, 'job_seeker_profile_id');
    }
}
