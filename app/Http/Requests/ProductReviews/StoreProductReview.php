<?php

namespace App\Http\Requests\ProductReviews;

use App\Helpers\CustomFormRequest;

class StoreProductReview extends CustomFormRequest
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
            'id' => 'bail|numeric|required',
            // 'approved_by' => 'bail|numeric|required',
            // 'approved_at' => 'bail|date|required',
            'note' => 'bail|string|nullable',

        ];
    }
}



