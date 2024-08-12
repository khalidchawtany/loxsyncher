<?php

namespace App\Http\Requests\PermissionsDescriptions;

use App\Helpers\CustomFormRequest;

class RemovePermissionsDescription extends CustomFormRequest
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
