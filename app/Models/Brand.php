<?php

namespace App\Models;

/**
 * @mixin IdeHelperBrand
 */
class Brand extends BaseModel
{
    protected $fillable = [
        'name',
        'company',
        'product_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
