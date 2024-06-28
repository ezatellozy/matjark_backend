<?php

namespace App\Http\Requests\Api\App\Order;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Address;
use App\Models\Coupon;
use App\Traits\OrderOperation;
use Illuminate\Http\Exceptions\HttpResponseException;


class OrderRequest extends ApiMasterRequest
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
            'address_id' => 'required|exists:addresses,id,user_id,' . auth('api')->id(),
            'pay_type' => 'required|in:wallet,card,cash',
            'code' =>  'nullable|exists:coupons,code',
            'address_data' => 'nullable',
            'comment' => 'nullable|min:2|max:10000',
            //code  of coupon  coupon data 
        ];
        $cart = auth()->guard('api')->user()->cart;
        if (auth()->guard('api')->user()->cart == null) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.carts.cart_is_empty'),
                'data' => null,
            ], 422));
        }
        if ($this->productHasValues($cart->cartProducts) == false) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.carts.check_the_quantity_of_the_products'),
                'data' => null,
            ], 422));
        }
        if ($this->code != null && Coupon::where('code', $this->code)->first() != null) {
            $coupon = Coupon::where('code', $this->code)->first();
            $message = $this->CheckCouponIsValide($coupon, null, $cart);
            if ($message  != null) {
                throw new HttpResponseException(response()->json([
                    'status' => 'fail',
                    'message' =>  $message,
                    'data' => null,
                ], 422));
            }
        }

        $offerMessage = $this->CheckOfferIsValide('order', null, $this->pay_type , $this->code);
        if ($offerMessage != null) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' => $offerMessage,
                'data' => null,
            ], 422));
        }

        $flashSaleMessage =  $this->CheckFlashSaleIsValide('order');
        if ($flashSaleMessage != null) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' => $flashSaleMessage,
                'data' => null,
            ], 422));
        }

        $priceData =   $this->calculationItemsPrice(auth()->guard('api')->user()->cart, ($this->code != null && Coupon::where('code', $this->code)->first() != null) ? Coupon::where('code', $this->code)->first() : null);

        if ($this->pay_type  == 'wallet' && auth()->guard('api')->user()->wallet->balance     < $priceData['total']) {

            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' => trans('app.messages.there_is_not_enough_balance'),
                'data' => null,
            ], 422));
        }

        return $data;
    }

    public function getValidatorInstance()
    {
        $data = $this->all();
        $data['address_data'] =  isset($data['address_id'])  && Address::where(['id' => $data['address_id'], 'user_id' => auth('api')->id()])->first() != null  ? Address::where(['id' => $data['address_id'], 'user_id' => auth('api')->id()])->first()->toJson() : null;
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
