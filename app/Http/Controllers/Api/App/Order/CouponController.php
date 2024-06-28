<?php

namespace App\Http\Controllers\Api\App\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\Order\{CheckCouponRequest};
use App\Http\Resources\Api\App\Cart\CartResource;
use App\Http\Resources\Api\App\Coupon\CouponResource;
use App\Http\Resources\Api\App\Product\SimpleProductDetailsResource;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Cart as ModelsCart;
use App\Models\Category;
use App\Models\ProductDetails;
use App\Traits\Cart;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    use Cart;

    public function cartResponse($message, $coupon = null, $guestToken = null, $addressId = null)
    {
        $cart = auth('api')->id() != null ? ModelsCart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() : ModelsCart::where(['guest_token' => $guestToken, 'user_id' => null])->first();
        if ($cart == null) {
            return response()->json([
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
            if ((auth()->guard('api')->user() != null && $addressId != null) || (auth()->guard('api')->user() != null && Address::where(['user_id' => auth('api')->id(), 'is_default' => 1])->first() != null)) {
                $address = $addressId != null ? Address::where(['user_id' => auth('api')->id(), 'id' => $addressId])->first() : Address::where(['user_id' => auth('api')->id(), 'is_default' => 1])->first();
            }
            $price = $this->calculationItemsPrice($cart, $coupon, $address, $guestToken);
            return response()->json([
                'data' => new CartResource($cart),
                'status' => 'success',
                'message' => $message,
                'coupon_price' => round($price['coupon_price'], 2),
                'offer_price' => 0,
                'discount' => 0,
                'vat_percentage' => setting('vat_percentage') != null ? (int)setting('vat_percentage') : 1,
                'currency' => 'SAR',
                'vat_price' => round($price['vat'], 2),
                'shipping_price' => round($price['shipping'], 2),
                'total_product' => round($price['sub_total'], 2),
                'sub_total_after_discount' => round( ($price['coupon_price'] < $price['sub_total']) ? ($price['sub_total'] - $price['coupon_price']) : 0, 2),
                'total_price' => round($price['total'], 2),
                'count_of_cart' => $cart->cartProducts()->sum('quantity'),
            ]);
        }
    }

    public function checkCoupon(CheckCouponRequest $request)
    {
//        $coupon = Coupon::where('code', $request->code)->firstOrFail();
        $coupon = Coupon::where('code', $request->code)->first();
        // if (!$coupon) {
        //     return response()->json([
        //         'data' => null,
        //         'message' => trans('app.messages.carts.invalid_voucher'),
        //         'status' => 'fail'
        //     ], 422);
        // }
        if (!$coupon) {
            return $this->cartResponse('', $coupon, $request->guest_token, $request->address_id);
        }
        return $this->cartResponse('', $coupon, $request->guest_token, $request->address_id);
    }

    public function index()
    {
        $now = Carbon::now();

        $coupons = Coupon::where('is_active', 1)
            // ->where('num_of_used', '>', 0)
            ->whereDate('start_at', '<=', $now)
            ->whereDate('end_at', '>=', $now)
            ->paginate(6);
        // dd(        $coupons );
        return (CouponResource::collection($coupons))->additional(['status' => 'success', 'message' => '']);
    }


    public function show($coupon_id)
    {
        $coupon = Coupon::where(['is_active' => true, 'id' => $coupon_id])->whereIn('applly_coupon_on', ['special_products', 'special_categories'])->firstOrFail();
        $products = [];
        if ($coupon->applly_coupon_on == 'special_products') {
            // dd($coupon->apply_ids);
            $products = ProductDetails::whereIn('id', $coupon->apply_ids)->where('quantity', '>=', 0)->whereHas('product', function ($q) {
                $q->where('is_active', true);
            })->get();
            // return (SimpleProductDetailsResource::collection($products))->additional(['status' => 'success', 'message' => '']);
        } elseif ($coupon->applly_coupon_on == 'special_categories') {
            $lastLevel = collect();
            $categoriesId = $coupon->apply_ids;
            foreach ($categoriesId as $category) {
                $cat = Category::find($category);
                if ($cat) {
                    $lastLevel->push(lastLevel($cat));
                }
            }

            $products = ProductDetails::where('quantity', '>=', 0)->whereHas('product', function ($q) use ($lastLevel) {
                $q->where('is_active', true);
                $q->whereHas('categoryProducts', function ($q) use ($lastLevel) {
                    $q->whereIn('category_id', $lastLevel->flatten()->unique()->pluck('id')->toArray());
                });
            })->get();

        }
        return (SimpleProductDetailsResource::collection($products))->additional(['status' => 'success', 'message' => '']);
    }
}
