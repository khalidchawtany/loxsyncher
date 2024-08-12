<?php

namespace App\Models;


/**
 * @mixin IdeHelperCustomsProduct
 */
class CustomsProduct extends BaseModel
{
    protected $fillable = [
        'name',
        'custom_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
