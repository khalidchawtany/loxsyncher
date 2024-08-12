<?php

namespace App\Http\Requests\Specifications;

use App\Helpers\CustomFormRequest;

class StoreSpecification extends CustomFormRequest
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

            'category' => 'bail|string',
            'title' => 'bail|string',
            'number' => 'bail|string',
            'standard' => 'bail|string',
            'status' => 'bail|string',
            'note' => 'bail|string',

        ];
    }
}
