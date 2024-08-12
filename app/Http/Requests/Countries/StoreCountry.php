<?php

namespace App\Http\Requests\Countries;

use App\Helpers\CustomFormRequest;

class StoreCountry extends CustomFormRequest
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

            'name' => 'bail|string',

        ];
    }
}
