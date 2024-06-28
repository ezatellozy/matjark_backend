<?php

namespace App\Http\Requests\Api\App\Cart;

use App\Models\{BuyToGetOffer, CartProduct, FlashSaleProduct, Offer, ProductDetails};
use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class CartProductCountRequest extends ApiMasterRequest
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
        $cartProduct = CartProduct::where('id' ,$this->cart_product_id )->whereHas('cart', function($q){
            $q->where(['user_id'=>auth('api')->id(),'guest_token' => null ])
            ->orWhere(['user_id'=>null,'guest_token' => $this->guest_token ]);
        })->firstOrFail();
        $productDetail = ProductDetails::findOrFail($cartProduct->product_detail_id);
        // dd(           $productDetail);
        $flashSaleProductDetail = FlashSaleProduct::find($cartProduct->flash_sale_product_id);
        $data = [
        'count' => 'required|numeric|min:1',
        'cart_product_id' => 'required|exists:cart_products,id',
        'user_id' => 'nullable',
        'guest_token' => 'nullable|required_if:user_id,null|exists:carts,guest_token',
        ];
// dd($cartProduct->flash_sale_product_id != null && ($flashSaleProductDetail->quantity  -  $flashSaleProductDetail->sold) < $this->count  && ($productDetail->quantity  -  $flashSaleProductDetail->sold) < $this->count);
        if ($cartProduct->flash_sale_product_id != null && ($flashSaleProductDetail->quantity  -  $flashSaleProductDetail->sold) < $this->count  && ($productDetail->quantity  -  $flashSaleProductDetail->sold) < $this->count) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.carts.needed_quantity_does_not_available'),
                'data' => null,
            ], 422));
        }
        if ($cartProduct->flash_sale_product_id != null && $flashSaleProductDetail->quantity_for_user< $this->count ) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.carts.needed_quantity_does_not_available'),
                'data' => null,
            ], 422));
        }

        if ($cartProduct->flash_sale_product_id != null && $flashSaleProductDetail->quantity_for_user< $this->count ) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.carts.needed_quantity_does_not_available'),
                'data' => null,
            ], 422));
        }

        // dd($productDetail->quantity  < $this->count);
        if ($productDetail->quantity  < $this->count) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.carts.needed_quantity_does_not_available'),
                'data' => null,
            ], 422));
        }
        return $data;
    }

    public function getValidatorInstance()
    {
        $data = $this->all();
        $data['user_id'] = auth()->guard('api')->user()  ? auth('api')->id() :  null;
        // dd(       auth()->guard('api')->user() );
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
