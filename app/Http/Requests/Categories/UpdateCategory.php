<?php

namespace App\Http\Requests\Categories;

use App\Helpers\CustomFormRequest;

class UpdateCategory extends CustomFormRequest
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
        ];
    }
}
