<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;

/**
 * @mixin IdeHelperProductCheckType
 */
class ProductCheckType extends BaseModel
{
    // use HasCompositePrimaryKey;

    protected $table = 'product_check_type';

    // protected $primaryKey = ['product_id', 'check_type_id'];

    // public $incrementing = false;

    protected $fillable = [
        'product_id',
        'check_type_id',
        'check_methods',
        'check_limits',
        'check_normal_range',
        'active',
        'order',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function checkType()
    {
        return $this->belongsTo(CheckType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query, $active = true)
    {
        return $query->where('active', $active);
    }

    public function scopeDisabled($query)
    {
        return $query->active(false);
    }

    public function scopeOfMyLabs($query)
    {
        return $query->whereIn('check_types.category', Lab::GetUserLabItems());
    }
}
