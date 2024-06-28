<?php

namespace App\Http\Requests\Api\Website\ReturnProduct;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\OrderProduct;

class ReturnProductRequest extends ApiMasterRequest
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
            'order_id' => 'required|exists:orders,id',
            'note' => 'nullable|string|max:255',
            'images' => 'nullable|array|min:1',
            'images.*' => 'nullable|mimes:jpg,jpeg,png,webp',
            "order_products"    => "required|array|min:1",
            "order_products.*.order_product_id"  => "required|exists:order_products,id",
            "order_products.*.quantity"  => "required|numeric|min:1",
        ];
    }
}
