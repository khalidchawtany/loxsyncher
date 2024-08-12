<?php

namespace App\Http\Requests\CustomsProducts;

use App\Helpers\CustomFormRequest;

class UpdateCustomsProduct extends CustomFormRequest
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
            'name' => 'bail|required|max:255',
            'custom_id' => 'bail|string|nullable|max:255',
        ];
    }
}
