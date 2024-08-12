<?php

namespace App\Models;

/**
 * @mixin IdeHelperSpecification
 */
class Specification extends BaseModel
{

    protected $fillable = [
        'category',
        'title',
        'title_eng',
        'number',
        'standard',
        'status',
        'document_url',
        'note',
        'user_id',
    ];
}

