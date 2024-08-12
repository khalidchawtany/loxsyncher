<?php

namespace App\Models;

/**
 * @mixin IdeHelperPermissionsDescription
 */
class PermissionsDescription extends BaseModel
{

    protected $fillable = [
        'permission_name',
        'description',
        'note',
        'user_id',
    ];
}

