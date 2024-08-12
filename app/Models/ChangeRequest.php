<?php

namespace App\Models;


/**
 * @mixin IdeHelperChangeRequest
 */
class ChangeRequest extends BaseModel
{

    protected $fillable = [
        'title',
        'description',
        'note',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
