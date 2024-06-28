<?php

namespace App\Http\Requests\Api\Provider\ReturnOrder;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\ReturnOrder;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReturnOrderChangeStatusRequest extends ApiMasterRequest
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
        $returnOrder = ReturnOrder::where(['status' => 'waiting'])->findOrFail($this->return_order_id);

        if ( array_diff($returnOrder->returnOrderProducts()->pluck('id')->toArray(), collect($this->return_order_products)->pluck('return_order_product_id')->toArray()) )
        {
            throw new HttpResponseException(response()->json([
                'status'  => 'fail',
                'data'    => null,
                'message' => trans('dashboard.error.return_order_products_not_true'),
            ], 422));
        }

        return [
            'return_order_id'                                 => 'required|exists:return_orders,id,status,waiting',
            'return_order_products'                           => 'required|array|min:1',
            'return_order_products.*.return_order_product_id' => 'required|exists:return_order_products,id,status,waiting',
            "return_order_products.*.status"                  => "required|in:accepted,rejected",
            "return_order_products.*.reject_reason"           => "nullable",
        ];
    }
}
