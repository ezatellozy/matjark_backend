<?php

namespace App\Http\Controllers\Api\App\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\Cart\{CartProductCountRequest, CartRequest};
use App\Http\Resources\Api\App\Cart\CartResource;
use App\Models\{Address, Cart, CartOfferType, CartProduct, Coupon, Offer};
use App\Traits\Cart as TraitsCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use TraitsCart;
    public  function cartResponse($message, $coupon = null, $guestToken = null, $addressId = null)
    {
        // info('kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk');
        $cart = auth('api')->id() != null ?  Cart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() :  Cart::where(['guest_token' => $guestToken, 'user_id' => null])->first();
        if ($cart != null && $cart->cartProducts()->count() == 0) {
            $cart->delete();
            return   response()->json([
                'data' => null,
                'status' => 'success',
                'message' => trans('app.messages.carts.cart_is_empty'),
                'coupon_price' => 0,
                'offer_price' => 0,
                'discount' => 0,
                'currency' => 'SAR',
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
                'discount' => 0,
                'currency' => 'SAR',
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
            $price = $this->calculationItemsPrice($cart, $coupon, $address ,$guestToken);
            return  response()->json([
                'data' => new CartResource($cart),
                'status' => 'success',
                'message' =>  $message,
                'coupon_price' => round($price['coupon_price'], 2),
                'offer_price' => 0,
                'discount' => 0,
                'currency' => 'SAR',
                'vat_percentage' => setting('vat_percentage') != null ? (int)setting('vat_percentage') : 1,
                'vat_price' => round($price['vat'], 2),
                'shipping_price' => round($price['shipping'], 2),
                'total_product' => round($price['sub_total'], 2),
                'sub_total_after_discount' => round( ($price['coupon_price'] < $price['sub_total']) ? ($price['sub_total'] - $price['coupon_price']) : 0, 2),
                'total_price' => round($price['total'], 2),
                'count_of_cart' =>  $cart->cartProducts()->sum('quantity'),

                // 'total_product' => round($price['sub_total'], 2),
                // 'total_product' => round($price['sub_total'] + $price['coupon_price'], 2),
                // 'sub_total_after_discount' => round($price['sub_total'], 2),
            ]);
        }
    }
    public function index(Request $request)
    {
        $coupon = $request->code ? Coupon::where('code', $request->code)->first() : null;
        return  $this->cartResponse('', ($coupon != null ? $coupon : null), $request->guest_token,$request->address_id);
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
            // $cart = Cart::where(['user_id'=> auth('api')->id() , 'guest_token' => null])->orWhere(['guest_token'=> $request->guest_token , 'user_id' =>null])->firstOrFail();
            \DB::commit();
            // $cart->refresh();
            return  $this->cartResponse(trans('app.messages.added_successfully'), null, $request->guest_token,  $request->address_id);
        } catch (\Exception $e) {
            \DB::rollback();
            dd($e);
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }


    public function deleteItem(Request $request, $deleteItemId)
    {
        $cartProduct =   CartProduct::findOrFail($deleteItemId);
        $cart = auth('api')->id() ?  Cart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() : Cart::where(['guest_token' => $request->guest_token, 'user_id' => null])->first();

        if ($cart  && $cart->cartProducts()->count() <= 1) {
            $cart->delete();
            return  $this->cartResponse(trans('app.messages.deleted_successfully'), null, $request->guest_token,  $request->address_id);
        } else {
            if ($cartProduct->offer_id != null) {
                $offer = Offer::findOrFail($cartProduct->offer_id);
                if ($offer->type == 'buy_x_get_y') {
                    $cartOfferBuy = CartOfferType::where(['offer_id' => $offer->id, 'cart_id' => $cart->id, 'cart_product_id' => $cartProduct->id, 'type' => 'buy_x'])->first();
                    if ($cartOfferBuy != null) {
                        $cartOfferGet =      CartOfferType::where(['offer_id' => $offer->id, 'cart_id' => $cart->id,  'type' => 'get_y'])->get();
                        if ($cartOfferGet->count() > 0) {
                            foreach ($cartOfferGet as $item) {
                                $item->cartProduct()->delete();
                                $item->delete();
                            }
                        }
                    }
                }
            }
            $cartProduct->delete();
        }
        return  $this->cartResponse(trans('app.messages.deleted_successfully'), null, $request->guest_token,  $request->address_id);
    }

    public function cartProductCount(CartProductCountRequest $request)
    {
        $cartProduct = CartProduct::where('id', $request->cart_product_id)
            // ->whereHas('cart', function ($q)  use ($request) {
            //     $q->where(['user_id' => auth('api')->id(), 'guest_token' => null])
            //         ->orWhere(['user_id' => null, 'guest_token' => $request->guest_token]);
            // })
            ->firstOrFail();
        try {
            $cartProductQuantity  =  $cartProduct->quantity;
            // dd($cartProductQuantity);
            $cartProduct->update([
                'quantity' => $request->count,
            ]);
            if ($cartProduct->offer_id != null) {

                $offer = Offer::findOrFail($cartProduct->offer_id);
                if ($offer->type == 'buy_x_get_y') {
                    // new code for offer
                    // dd($cartProductQuantity > $request->count);
                    if (($cartProductQuantity  >  $request->count) == true) {
                        $removeCount = $cartProductQuantity  - $request->count;
                        // dd(CartOfferType::where(['cart_product_id' => $cartProduct->id , 'type' => 'get_y'])->get());
                        $get =  CartOfferType::where(['cart_product_id' => $cartProduct->id, 'type' => 'get_y'])->get();
                        $buy =  CartOfferType::where(['cart_product_id' => $cartProduct->id, 'type' => 'buy_x'])->get();
                        if ($get) {

                            foreach ($get as $k) {
                                $count = $k->quantity;
                                if ($k->quantity >   $removeCount) {
                                    //$k->decrement(['quantity', $removeCount]);
                                    $k->update(['quantity' => $k->quantity - $removeCount]);
                                    $removeCount = $count - $removeCount;
                                } elseif ($k->quantity ==   $removeCount) {
                                    $k->delete();
                                    $removeCount = 0;
                                } elseif ($k->quantity <   $removeCount) {
                                    $k->delete();
                                    $removeCount = $removeCount - $count;
                                }
                            }
                        }
                        if ($buy &&             $removeCount > 0) {
                            foreach ($buy as $k) {
                                $count = $k->quantity;
                                if ($k->quantity >   $removeCount) {
                                    //$k->decrement(['quantity', $removeCount]);
                                    $k->update(['quantity' => $k->quantity - $removeCount]);
                                    $removeCount = $count - $removeCount;
                                } elseif ($k->quantity ==   $removeCount) {
                                    $k->delete();
                                    $removeCount = 0;
                                } elseif ($k->quantity <   $removeCount) {
                                    $k->delete();
                                    $removeCount = $removeCount - $count;
                                }
                            }
                        }
                    } else {
                        $this->offerbuyXGetY($cartProduct->offer_id, $cartProduct, $cartProduct->product_detail_id,     $cartProduct->cart, ($request->count -   $cartProductQuantity), $request->count, $cartProductQuantity);
                    }
                }
            }
            $cart =   $cartProduct->cart;
            $cart->refresh();
            return  $this->cartResponse(trans('app.messages.added_successfully'), null, $request->guest_token,  $request->address_id);
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
        return  $this->cartResponse(trans('app.messages.deleted_successfully'), null, $request->guest_token,  $request->address_id);
    }
}
