<?php

namespace App\Models;


/**
 * @mixin IdeHelperCategory
 */
class Category extends BaseModel
{

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
