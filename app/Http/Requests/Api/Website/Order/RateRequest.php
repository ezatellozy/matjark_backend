<?php

namespace App\Http\Requests\Api\Website\Order;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\{Order,OrderRate};
use Illuminate\Http\Exceptions\HttpResponseException;


class RateRequest extends ApiMasterRequest
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
        $data =  [
            'rate' => 'required|numeric|between:1,5',
            'comment' => 'nullable|string|max:255',
            'product_detail_id' => 'required|exists:product_details,id',
            'rate_images' => 'nullable|array|min:1',
            'rate_images.*' => 'nullable|mimes:jpg,jpeg,png,webp',
        ];

        $order = Order::findOrFail($this->route('order_id'));

        if ($order->status != 'admin_delivered') {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.incomplete_order'),
                'data' => null,
            ], 422));
        }

        if (OrderRate::where(['user_id' => auth('api')->id(), 'product_detail_id' => $this->product_detail_id, 'order_id' => $this->route('order_id')])->first() != null) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.it_is_not_possible_to_evaluate_more_than_once'),
                'data' => null,
            ], 422));
        }
        
        return $data;    
    }
}
