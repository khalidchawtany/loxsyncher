<?php

namespace App\Models;

/**
 * @mixin IdeHelperCountry
 */
class Country extends BaseModel
{

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDefaultCountry($query)
    {
        return $query->where('is_default', 1);
    }
}
