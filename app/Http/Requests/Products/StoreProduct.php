<?php

namespace App\Http\Requests\Products;

use App\Helpers\CustomFormRequest;

class StoreProduct extends CustomFormRequest
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
            'name' => 'bail|required|max:255|unique:products',
            'kurdish_name' => 'bail|required|max:255|unique:products',
            'alternative_names' => 'bail|string|nullable',
            'arabic_name' => 'bail|max:255',
            'customs_name' => 'bail|string|nullable',
            'coc' => 'bail|boolean|nullable',
            'hide_regapedan' => 'bail|boolean|nullable',
            'skip_payment' => 'bail|boolean|nullable',
            'delay_results' => 'bail|boolean|nullable',
            'disabled' => 'bail|boolean|nullable',
            'blended' => 'bail|boolean|nullable',
            'fee_limit' => 'bail|numeric|min:0|required_with_all:fee_if_less,fee_if_more|nullable',
            'fee_if_less' => 'bail|numeric|min:0|required_with_all:fee_limit,fee_if_more|nullable',
            'fee_if_more' => 'bail|numeric|min:0|required_with_all:fee_limit,fee_if_less|nullable',
            'invoice_copies' => 'bail|numeric|min:0|nullable',
        ];
    }
}
