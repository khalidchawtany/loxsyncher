<?php

namespace App\Http\Requests\AppSettings;

use App\Helpers\CustomFormRequest;

class StoreAppSetting extends CustomFormRequest
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
            'value' => 'bail|string|nullable',

        ];
    }
}
