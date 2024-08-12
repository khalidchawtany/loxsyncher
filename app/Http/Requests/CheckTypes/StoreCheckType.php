<?php

namespace App\Http\Requests\CheckTypes;

use App\Helpers\CustomFormRequest;

class StoreCheckType extends CustomFormRequest
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

            'category' => 'bail|required|max:255',

            'subcategory' => 'bail|max:255|unique_with:check_types,category,subcategory',

            'disabled' => 'bail|boolean|nullable',

            'price' => 'bail|required|numeric',

        ];
    }

    public function messages()
    {
        return [
            'subcategory.unique_with' => 'The check type already exists',
        ];
    }
}
