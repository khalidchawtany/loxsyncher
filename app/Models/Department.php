<?php

namespace App\Models;

/**
 * @mixin IdeHelperDepartment
 */
class Department extends BaseModel
{
    protected $fillable = [
        'name',
        'kurdish_name',
        'manager_name',
        'to',
        'to_arabic',
        'props',
        'sample_count',
        'note',
        'user_id',
        'is_third_party',
        'needs_inspections_approved',
        'delays_results',
        'permit_copies',
        'transaction_copies',
        'failed_transaction_copies',
        'invoice_copies',
    ];

    public static $FOOD_DEPARTMENT_ID = 4;

    public static $OTHER_DEPARTMENT_ID = 0;

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public static function WithDelayedTestResults()
    {
        return self::select(['id'])
            ->where(['delays_results' => true])
            ->get()
            ->pluck('id');
    }

    public function has($prop)
    {
        $dep_props = $this->props;

        /* $dep_props = [ */
        /*     //visual_inspection, destination, country, Balance, Batch, Merchant, Office bit sequence */
        /*     'Construction'    =>15, //'0111010' */
        /*     'Agriculture'     =>2,  //'0000010' */
        /*     'Fuel'            =>2,  //'0000010' */
        /*     'Quality Control' =>6,  //'0000110' */
        /*     'Veterinary'      =>3,  //'0000011' */
        /*     'Food'            =>71  //'1000111' */
        /* ]; */

        switch ($prop) {
                // This causes the combining batches with transactions based on date and truck_plate ignoring the product_id
                // of the batch. And then setting the batches product_id to the one of the transaction
            case 'ignore_batch_product_id':
                return $dep_props & 256;
            case 'delayed_test_results': //Causes printing different document when transaction is paid for
                return $dep_props & 128;
            case 'visual_inspection':
                return $dep_props & 64;
            case 'destination':
                return $dep_props & 32;
            case 'country':
                return $dep_props & 16;
            case 'balance':
                return $dep_props & 8;
            case 'batch':
                return $dep_props & 4;
            case 'merchant':
                return $dep_props & 2;
            case 'office':
                return $dep_props & 1;
        }
    }
}
