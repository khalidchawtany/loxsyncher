<?php

namespace App\Http\Requests\Users;

use App\Helpers\CustomFormRequest;

class StoreUser extends CustomFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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

            'name' => 'bail|required|max:255|unique:users',

            'email' => 'bail|required|email|unique:users',

            'role' => 'bail|nullable',

            'department' => 'bail|required',

            'agent' => 'bail|required_if:role,==,Agent',

            'is_staff' => 'bail|boolean|nullable',

            'job_description' => 'bail|string|required',

            'external_view' => 'bail|boolean|required',

            'external_update' => 'bail|boolean|required',

        ];
    }
}
