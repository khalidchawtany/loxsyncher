<?php

namespace App\Http\Requests\CheckTypes;

class UpdateCheckType extends StoreCheckType
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

            'category' => 'bail|required|max:255',

            'subcategory' => 'bail|max:255|unique_with:check_types,category,subcategory,' . $this->get('id'),

            'disabled' => 'bail|boolean|nullable',

            'price' => 'bail|required|numeric',

        ];
    }
}
