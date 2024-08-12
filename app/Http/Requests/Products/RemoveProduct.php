<?php

namespace App\Http\Requests\Products;

use App\Helpers\CustomFormRequest;

class RemoveProduct extends CustomFormRequest
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

            'id' => 'bail|required|numeric',

        ];
    }
}
