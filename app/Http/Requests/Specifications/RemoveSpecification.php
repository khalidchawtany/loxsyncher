<?php

namespace App\Http\Requests\Specifications;

use App\Helpers\CustomFormRequest;

class RemoveSpecification extends CustomFormRequest
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

            'id' => 'bail|required',

        ];
    }
}
