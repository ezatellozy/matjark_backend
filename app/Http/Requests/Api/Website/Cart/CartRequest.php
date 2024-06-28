<?php

namespace App\Http\Requests\Api\Website\Cart;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\{Cart, CartOfferType, FlashSaleProduct, CartProduct, Offer, ProductDetails};
use App\Traits\OrderOperation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class CartRequest extends ApiMasterRequest
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
        $required = 'required';
        if ($this->flash_sale_product_id != null) {
            $required = 'nullable';
        }
        $cartProduct =  null;
        if (auth()->guard('api')->user() != null) {

            $cartProduct = CartProduct::where('product_detail_id', $this->product_detail_id)->whereHas('cart', function ($q) {
                $q->where(['user_id' => auth('api')->id(), 'guest_token' => null]);
                // ->orWhere(['user_id' => null, 'guest_token' => $this->guest_token]);
            })->first();
        } else {
            $cartProduct = CartProduct::where('product_detail_id', $this->product_detail_id)->whereHas('cart', function ($q) {
                // $q->where(['user_id' => auth('api')->id(), 'guest_token' => null])
                $q->where(['user_id' => null, 'guest_token' => $this->guest_token]);
            })->first();
        }


        $data = [
            'product_detail_id' =>     $required . '|exists:product_details,id',
            'quantity' => 'required|numeric|min:1',
            'offer_id' => 'nullable|exists:offers,id',
            'flash_sale_product_id' => 'nullable|exists:flash_sale_products,id',
            'user_id' => 'nullable',
            'guest_token' => 'nullable|required_if:user_id,null',
        ];
        // new code for offer 
        if (auth()->guard('api')->user() != null || $this->guest_token != null) {

            $cart = auth('api')->id() != null ?  Cart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() :  Cart::where(['guest_token' => $this->guest_token, 'user_id' => null])->first();
            
            if ($cart  != null) {

                $offer = $this->offer_id != null ? Offer::find($this->offer_id) : null;

                if ($offer != null && $offer->type == 'buy_x_get_y') {
                    $cartOfferType = CartOfferType::where(['cart_id' =>  $cart->id])->where('offer_id', '!=', $this->offer_id)->first();

                    if ($cartOfferType != null) {
                        throw new HttpResponseException(response()->json([
                            'status' => 'fail',
                            'message' =>  trans('app.messages.carts.you_cannot_add_this_type_of_offer_again'),
                            'data' => null,
                        ], 422));
                    }
                }
            }
        }

        $flashSaleProductDetail = FlashSaleProduct::find($this->flash_sale_product_id);
        $productDetail = $this->flash_sale_product_id ? optional(FlashSaleProduct::find($this->flash_sale_product_id))->productDetail : ProductDetails::findOrFail($this->product_detail_id);

        $quantity  = $this->quantity;
        if ($cartProduct != null) {
            $quantity = $cartProduct->quantity +  $this->quantity;
        }
        // dd( $quantity );
        if ($this->flash_sale_product_id != null && ($flashSaleProductDetail->quantity  -  $flashSaleProductDetail->sold) <  $quantity  && ($productDetail->quantity  -  $flashSaleProductDetail->sold) <  $quantity) {
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
        if ($this->flash_sale_product_id != null && $flashSaleProductDetail->quantity_for_user <  $quantity) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.carts.needed_quantity_does_not_available'),
                'data' => null,
            ], 422));
        }
        $offerMessage = $this->offer_id != null ?  $this->CheckOfferIsValide('cart', $this->offer_id, null, request()->code) :  null;
        // dd($offerMessage );

        if ($offerMessage != null) {

            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' => $offerMessage,
                'data' => null,
            ], 422));
        }

        $flashSaleMessage = $this->flash_sale_product_id != null ?  $this->CheckFlashSaleIsValide('cart', $this->flash_sale_product_id) : null;
        if ($flashSaleMessage != null) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' => $flashSaleMessage,
                'data' => null,
            ], 422));
        }
        return $data;
    }
    public function getValidatorInstance()
    {
        $data = $this->all();

        $data['user_id'] = auth()->guard('api')->user()  ? auth('api')->id() :  null;
        if (isset($data['flash_sale_product_id'])) {
            $data['product_detail_id'] = FlashSaleProduct::find($data['flash_sale_product_id']) ? FlashSaleProduct::find($data['flash_sale_product_id'])->product_detail_id : null;
        }
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
