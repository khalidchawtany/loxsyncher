<?php

namespace App\Http\Requests\CheckTypes;

use App\Helpers\CustomFormRequest;

class RemoveCheckType extends CustomFormRequest
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
