<?php

namespace App\Models;

/**
 * @mixin IdeHelperDestination
 */
class Destination extends BaseModel
{

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function scopeDefaultDestination($query)
    {
        return $query->where('is_default', 1);
    }
}
