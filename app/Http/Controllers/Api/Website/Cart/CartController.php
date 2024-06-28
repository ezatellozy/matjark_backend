<?php

namespace App\Http\Controllers\Api\Website\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Website\Cart\{CartProductCountRequest, CartRequest};
use App\Http\Resources\Api\Website\Cart\CartResource;
use App\Models\{Address, Cart, CartOfferType, CartProduct, Coupon, Offer};
use App\Traits\Cart as TraitsCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use TraitsCart;

    public  function cartResponse($message, $coupon = null, $guestToken = null, $addressId = null)
    {
        $cart = auth('api')->id() != null ?  Cart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() :  Cart::where(['guest_token' => $guestToken, 'user_id' => null])->first();

        if ($cart != null && $cart->cartProducts()->count() == 0) {
            $cart->delete();
            return   response()->json([
                'data' => null,
                'status' => 'success',
                'message' => trans('app.messages.carts.cart_is_empty'),
                'coupon_price' => 0,
                'offer_price' => 0,
                'currency' => 'SAR',
                'discount' => 0,
                'vat_percentage' => 0,
                'vat_price' => 0,
                'shipping_price' => 0,
                'total_product' => 0,
                'sub_total_after_discount' => 0,
                'total_price' => 0,
                'count_of_cart' => 0,
            ]);
        }
        if ($cart == null) {
            return   response()->json([
                'data' => null,
                'status' => 'success',
                'message' => trans('app.messages.carts.cart_is_empty'),
                'coupon_price' => 0,
                'offer_price' => 0,
                'currency' => 'SAR',
                'discount' => 0,
                'vat_percentage' => 0,
                'vat_price' => 0,
                'shipping_price' => 0,
                'total_product' => 0,
                'sub_total_after_discount' => 0,
                'total_price' => 0,
                'count_of_cart' => 0,
            ]);
        } else {

            $address = null;

            if ((auth()->guard('api')->user() != null && $addressId  != null) || (auth()->guard('api')->user() != null && Address::where(['user_id' => auth('api')->id(), 'is_default' => 1])->first() != null)) {
                $address = $addressId != null ? Address::where(['user_id' => auth('api')->id(), 'id' => $addressId])->first() :  Address::where(['user_id' => auth('api')->id(), 'is_default' => 1])->first();
            }

            $price = $this->calculationItemsPrice($cart, $coupon, $address , $guestToken);

            return  response()->json([
                'data' => new CartResource($cart),
                'status' => 'success',
                'message' =>  $message,
                'coupon_price' => round($price['coupon_price'], 2),
                'offer_price' => 0,
                'currency' => 'SAR',
                'discount' => 0,
                'vat_percentage' => setting('vat_percentage') != null ? (int)setting('vat_percentage') : 1,
                'vat_price' => round($price['vat'], 2),
                'shipping_price' => round($price['shipping'], 2),
                // 'total_product' => round($price['sub_total'] + $price['coupon_price'], 2),
                // 'sub_total_after_discount' => round($price['sub_total'], 2),
                'total_product' => round($price['sub_total'], 2),
                'sub_total_after_discount' => round( ($price['coupon_price'] < $price['sub_total']) ? ($price['sub_total'] - $price['coupon_price']) : 0, 2),
                'total_price' => round($price['total'], 2),
                'count_of_cart' =>  $cart->cartProducts()->sum('quantity'),
            ]);
        }
    }

    public function index(Request $request)
    {
        $coupon = $request->code ? Coupon::where('code', $request->code)->first() : null;
        return  $this->cartResponse('', ($coupon != null ? $coupon : null), $request->guest_token, $request->address_id);
    }
    public function store(CartRequest $request)
    {
        \DB::beginTransaction();
        try {
            if (auth()->guard('api')->user() == null && $request->guest_token != null) {
                $this->guestNewCart($request);
            } else {
                $this->useNewCart($request);
            }

            RemoveZeroQty();

            \DB::commit();
            return  $this->cartResponse(trans('app.messages.added_successfully'), null, $request->guest_token,   $request->address_id);
        } catch (\Exception $e) {
            \DB::rollback();
            dd($e);
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }


    public function deleteItem(Request $request, $deleteItemId)
    {
        $cartProduct =   CartProduct::find($deleteItemId);

        if($cartProduct == null) {

            return (response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.carts.cart_product_not_found'),
                'data' => null,
            ], 422));

        }

        $cart = auth('api')->id() ?  Cart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() : Cart::where(['guest_token' => $request->guest_token, 'user_id' => null])->first();

        if ($cart  && $cart->cartProducts()->count() <= 1) {
            CartProduct::where('cart_id',$cart->id)->delete();
            $cart->delete();
            return  $this->cartResponse(trans('app.messages.deleted_successfully'), null, $request->guest_token, $request->address_id);
        } else {
            if ($cartProduct->offer_id != null) {
                $offer = Offer::findOrFail($cartProduct->offer_id);
                if ($offer->type == 'buy_x_get_y') {
                    $cartOfferBuy = CartOfferType::where(['offer_id' => $offer->id, 'cart_id' => $cart->id, 'cart_product_id' => $cartProduct->id, 'type' => 'buy_x'])->first();
                    if ($cartOfferBuy != null) {
                        $cartOfferGet =      CartOfferType::where(['offer_id' => $offer->id, 'cart_id' => $cart->id,  'type' => 'get_y'])->get();
                        if ($cartOfferGet->count() > 0  ) {
                            foreach($cartOfferGet as $item){
                                $item->cartProduct()->delete();
                                  $item->delete();
                            }
                        }
                    }
                }
            }
            CartProduct::where('product_detail_id',$cartProduct->product_detail_id)->delete();
            $cartProduct->delete();
        }
        return  $this->cartResponse(trans('app.messages.deleted_successfully'), null, $request->guest_token,   $request->address_id);
    }

    public function cartProductCount(CartProductCountRequest $request)
    {

        $cartProduct = CartProduct::where('id', $request->cart_product_id)->firstOrFail();

        try {
            $cartProductQuantity  =  $cartProduct->quantity;
            $cartProduct->update([
                'quantity' => $request->count,
            ]);
            if ($cartProduct->offer_id != null) {

                $offer = Offer::findOrFail($cartProduct->offer_id);
                if ($offer->type == 'buy_x_get_y') {
                    // new code for offer
                    $this->offerbuyXGetY($cartProduct->offer_id, $cartProduct, $cartProduct->product_detail_id,     $cartProduct->cart, ($request->count -   $cartProductQuantity));
                }
            }

            if($cartProduct->quantity == 0) {
                $cartProduct->delete();
            }

            $cart =   $cartProduct->cart;
            $cart->refresh();

            RemoveZeroQty();

            return  $this->cartResponse(trans('app.messages.added_successfully'), null, $request->guest_token,   $request->address_id);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }

    public function deleteAllCart(Request $request)
    {
        $cart = auth('api')->id() ? Cart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() : Cart::where(['guest_token' => $request->guest_token, 'user_id' => null])->first();
        if ($cart == null) {
            return  $this->cartResponse('');
        }
        $cart->delete();
        return  $this->cartResponse(trans('app.messages.deleted_successfully'), null, $request->guest_token,   $request->address_id);
    }
}
