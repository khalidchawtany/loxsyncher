<?php

namespace App\Http\Requests\PermissionsDescriptions;

use App\Helpers\CustomFormRequest;

class UpdatePermissionsDescription extends CustomFormRequest
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
            'permission_name' => 'bail|string|required',
            'description' => 'bail|string|required',
            'note' => 'bail|string|nullable',

        ];
    }
}
