<?php

namespace App\Models;

use App\Traits\HasSchemalessAttributes;


/**
 * @mixin IdeHelperProduct
 */
class Product extends BaseModel
{
    use HasSchemalessAttributes;

    protected $fillable = [
        'name',
        'kurdish_name',
        'alternative_names',
        'arabic_name',
        'customs_name',
        'coc',
        'fee_if_less', // fee if product amount less than fee_limit
        'fee_limit',
        'fee_if_more', // fee if product amount more than fee_limit
        'date_limit', // number of days
        'amount_limit',
        'requires_truck_limit',
        'is_paid_individually',
        'disabled',
        'blended',
        'hide_regapedan',
        'skip_payment',
        'delay_results',
        'note',
        'user_id',
        'department_id',
        'category_id',
        'extra',
        'invoice_copies',
    ];

    protected $schemalessAttributes = [
        'extra',
    ];

    /* public function scopeOfDepartments($query, $departments) */
    /* { */
    /*     return $query->whereIn('department_id', $departments); */
    /* } */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkTypes()
    {
        return $this->belongsToMany(CheckType::class, 'product_check_type');
    }

    public function activeCheckTypes()
    {
        return $this->belongsToMany(CheckType::class, 'product_check_type')
            ->where('active', true);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function balancedByAmount()
    {
        return $this->amount_limit != null
            && $this->amount_limit > 0;
    }

    public function balancedByTruckCount()
    {
        return $this->requires_truck_limit != null
            && $this->requires_truck_limit == true;
    }

    public function isBalanced()
    {
        return $this->balancedByAmount()
            || $this->balancedByTruckCount();
    }

    public function hasVisualInspectionOnly()
    {
        return $this->visualInspection() != null
            && $this->activeCheckTypes()->count() == 1;
    }

    public function visualInspection()
    {
        $productCheckTypes = $this->activeCheckTypes()->get();

        return $productCheckTypes->filter(function ($checkType) {
            return $checkType->isAutoCheck();
        })->first();
    }
}
