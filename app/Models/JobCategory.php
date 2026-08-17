<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobCategory extends Model
{
    protected $table = 'job_categories';
    protected $fillable = ['name', 'slug'];

    public function postings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }
}
