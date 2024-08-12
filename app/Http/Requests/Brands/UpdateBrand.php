<?php

namespace App\Http\Requests\Brands;

use App\Helpers\CustomFormRequest;

class UpdateBrand extends CustomFormRequest
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
            'id' => 'bail|numeric|required',
            'name' => 'bail|required|max:255',
            'company' => 'bail|required|max:255',
            'product_id' => 'numeric|required|exists:products,id',
        ];
    }
}
