<?php

namespace App\Http\Requests\Api\Dashboard\Report;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Category;

class SalesRequest extends ApiMasterRequest
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
            'sub_report_type' => 'required|in:product_sales,category_sales,city_sales',
        ];

        return $rules;
    }



}
