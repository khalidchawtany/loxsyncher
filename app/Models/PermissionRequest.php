<?php

namespace App\Models;


/**
 * @mixin IdeHelperPermissionRequest
 */
class PermissionRequest extends BaseModel
{

    protected $fillable = [
        'requested_for',
        'permission_name',
        'description',
        'note',
        'staus',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function for_user()
    {
        return $this->belongsTo(User::class, 'requested_for');
    }
}
