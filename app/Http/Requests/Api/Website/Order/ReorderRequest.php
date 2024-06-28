<?php

namespace App\Http\Requests\Api\Website\Order;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\FlashSaleProduct;
use App\Traits\OrderOperation;
use App\Models\Order;
use App\Models\ProductDetails;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReorderRequest extends ApiMasterRequest
{
    use OrderOperation;

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
        $data = [
            'order_id' => 'required|exists:orders,id'
        ];

        $order = Order::find($this->order_id);

        if ($order == null) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.order_not_found'),
                'data' => null,
            ], 422));
        }

        $orderProducts = $order->orderProducts;

        if ($orderProducts) {

            foreach ($orderProducts as $orderProduct) {

                $offerMessage = $orderProduct->offer_id != null ?  $this->CheckOfferIsValide('cart', $orderProduct->offer_id) :  null;

                if ($offerMessage != null) {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'message' => $offerMessage,
                        'data' => null,
                    ], 422));
                }

                $flashSaleMessage = $orderProduct->flash_sale_product_id != null ?  $this->CheckFlashSaleIsValide('cart', $orderProduct->flash_sale_product_id) : null;

                if ($flashSaleMessage != null) {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'message' => $flashSaleMessage,
                        'data' => null,
                    ], 422));
                }

                /* **************************************************************************** */

                $flashSaleProductDetail = FlashSaleProduct::find($orderProduct->flash_sale_product_id);
                $productDetail = $orderProduct->flash_sale_product_id ? optional(FlashSaleProduct::find($orderProduct->flash_sale_product_id))->productDetail : ProductDetails::findOrFail($orderProduct->product_detail_id);
                $cartProduct = auth()->guard('api')->user()->cart ? auth()->guard('api')->user()->cart->cartProducts()->where(['flash_sale_product_id' => $orderProduct->flash_sale_product_id, 'product_detail_id' => $orderProduct->product_detail_id])->first() : null;
    
                $quantity  = $orderProduct->quantity;
                if ($cartProduct != null) {
                    $quantity = $cartProduct->quantity +  $orderProduct->quantity;
                }
                if ($orderProduct->flash_sale_product_id != null && ($flashSaleProductDetail->quantity  -  $flashSaleProductDetail->sold) <  $quantity  && ($productDetail->quantity  -  $flashSaleProductDetail->sold) <  $quantity) {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'message' =>  trans('app.messages.carts.needed_quantity_does_not_available'),
                        'data' => null,
                    ], 422));
                }
                if ($productDetail->quantity  <    $quantity) {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'message' =>  trans('app.messages.carts.needed_quantity_does_not_available'),
                        'data' => null,
                    ], 422));
                }
                if ($orderProduct->flash_sale_product_id != null && $flashSaleProductDetail->quantity_for_user <  $quantity) {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'message' =>  trans('app.messages.carts.needed_quantity_does_not_available'),
                        'data' => null,
                    ], 422));
                }

            }



           
        }

        return $data;
    }
}
