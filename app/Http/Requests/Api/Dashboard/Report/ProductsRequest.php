<?php

namespace App\Http\Requests\Api\Dashboard\Report;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Category;

class ProductsRequest extends ApiMasterRequest
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

        $rules = [
            'sub_report_type' => 'required|in:most_sales_products,reminder_products',
        ];

        return $rules;
    }



}
