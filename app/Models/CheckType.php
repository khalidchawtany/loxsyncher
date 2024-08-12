<?php

namespace App\Models;

/**
 * @mixin IdeHelperCheckType
 */
class CheckType extends BaseModel
{
    protected $fillable = [
        'category',
        'subcategory',
        'disabled',
        'description',
        'price',
        'acronym',
        'note',
        'reason',
        'user_id',
    ];

    public const AUTO_CHECK = [
        'id' => 32,
        'category' => 'Physical',
        'subcategory' => 'Visual Inspection (Auto)',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_check_type');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isAutoCheck()
    {
        return $this->category == CheckType::AUTO_CHECK['category']
            && $this->subcategory == CheckType::AUTO_CHECK['subcategory'];
    }
}
