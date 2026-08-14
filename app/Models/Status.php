<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $fillable = ['type', 'slug', 'label', 'sort_order', 'is_terminal'];

    protected function casts(): array
    {
        return ['is_terminal' => 'boolean'];
    }

    public function transitionsFrom(): HasMany
    {
        return $this->hasMany(StatusTransition::class, 'from_status_id');
    }

    public function transitionsTo(): HasMany
    {
        return $this->hasMany(StatusTransition::class, 'to_status_id');
    }

    /**
     * Every status this one is legally allowed to move to next.
     */
    public function allowedNextStatuses()
    {
        return static::whereIn(
            'id',
            StatusTransition::where('from_status_id', $this->id)
                ->where('status_type', $this->type)
                ->pluck('to_status_id')
        )->orderBy('sort_order')->get();
    }
}
