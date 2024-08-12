<?php

namespace App\Http\Requests\Departments;

use App\Helpers\CustomFormRequest;

class StoreDepartment extends CustomFormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'name' => 'bail|required|max:255|unique:departments,name',

            'kurdish_name' => 'bail|required|max:255',

            'to' => 'bail|required|max:255',

            'to_arabic' => 'bail|required|max:255',

            'sample_count' => 'bail|numeric',

            'bg_img' => 'bail|string|nullable',

            'is_third_party' => 'bail|boolean|nullable',

            'needs_inspections_approved' => 'bail|boolean|nullable',

            'delays_results' => 'bail|boolean',

            'permit_copies' => 'bail|numeric',

            'transaction_copies' => 'bail|numeric',

            'failed_transaction_copies' => 'bail|numeric',

            'invoice_copies' => 'bail|numeric',
        ];
    }
}
