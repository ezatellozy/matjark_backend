<?php

namespace App\Traits;

use App\Http\Resources\Api\App\Cart\CartResource;
use App\Models\Cart as ModelsCart;
use App\Models\CartOfferType;
use App\Models\CartProduct;
use App\Models\FlashSaleOrder;
use App\Models\Offer;
use App\Models\OfferOrder;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductDetails;
use Carbon\Carbon;

trait Cart
{

    public  function responceForCart($message, $coupon = null, $guestToken = null, $lat = null, $lng = null)
    {

        $cart = auth('api')->id() != null ?  ModelsCart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() :  ModelsCart::where(['guest_token' => $guestToken, 'user_id' => null])->first();
        if ($cart == null) {
            return   response()->json([
                'data' => null,
                'status' => 'success',
                'message' => trans('app.messages.carts.cart_is_empty'),
                'coupon_price' => 0,
                'offer_price' => 0,
                'discount' => 0,
                'vat_percentage' => 0,
                'vat_price' => 0,
                'shipping_price' => 0,
                'total_product' => 0,
                'total_price' => 0,
                'count_of_cart' => 0,
            ]);
        } else {

            $price = $this->calculationItemsPrice($cart, $coupon, $lat, $lng);
            return  response()->json([
                'data' => new CartResource($cart),
                'status' => 'success',
                'message' =>  $message,
                'coupon_price' => round($price['coupon_price'], 2),
                'offer_price' => 0,
                'discount' => 0,
                'vat_percentage' => 0,
                'vat_price' => round($price['vat'], 2),
                'shipping_price' => round($price['shipping'], 2),
                'total_product' => round($price['sub_total'], 2),
                'total_price' => round($price['total'], 2),
                'count_of_cart' =>  $cart->cartProducts()->sum('quantity'),
            ]);
        }
    }

    public function vatPrice($totalPrice)
    {
        $vat_price = 0;
        $vat_percentage = setting('vat_percentage') != null ? setting('vat_percentage') : 1;
        $vat_price = ($vat_percentage * $totalPrice) / 100;
        $data = [
            'vat_percentage' =>  $vat_percentage,
            'vat_price' =>  $vat_price,
        ];
        return $data;
    }
    public function CouponPrice($coupon, $guestToken = null)
    {
        $couponPrice = 0;
        $totalPriceForProduct  = 0;

        $cart = auth()->guard('api')->user() != null ?  ModelsCart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() :  ModelsCart::where(['guest_token' => $guestToken, 'user_id' => null])->first();

        if ($coupon->applly_coupon_on == 'all') {
            // $products =  $cart->cartProducts()->where('offer_id', null)->orWhere('flash_sale_product_id', null)->get();
            $products =  $cart->cartProducts()->get();
            // foreach ($products as $product) {
            //     $totalPriceForProduct += ($product->quantity *     $product->productDetail->price);
            // }
        }
        if ($coupon->applly_coupon_on == 'special_products') {
            // $products =  $cart->cartProducts()->where('offer_id', null)->orWhere('flash_sale_product_id', null)->whereIn('product_detail_id', $coupon->apply_ids)->get();
            $products =  $cart->cartProducts()->whereIn('product_detail_id', $coupon->apply_ids)->get();
        }
        if ($coupon->applly_coupon_on == 'except_products' && auth()->guard('api')->user() != null) {
            $products =    $cart->cartProducts()->whereNotIn('product_detail_id', $coupon->apply_ids)->get();
            // $products =    $cart->cartProducts()->where('offer_id', null)->orWhere('flash_sale_product_id', null)->whereNotIn('product_detail_id', $coupon->apply_ids)->get() ;
        }
        if ($coupon->applly_coupon_on == 'special_categories') {
            // $products =  $cart->cartProducts()->where('offer_id', null)->orWhere('flash_sale_product_id', null)->whereHas('productDetail', function ($q) use ($coupon) {
            $products =  $cart->cartProducts()->whereHas('productDetail', function ($q) use ($coupon) {
                $q->whereHas('product', function ($q) use ($coupon) {
                    $q->whereHas('categoryProducts', function ($q) use ($coupon) {
                        $q->whereIn('category_id', $coupon->apply_ids);
                    });
                });
            });
        }
        if ($coupon->applly_coupon_on == 'except_categories') {
            // $products =  $cart->cartProducts()->where('offer_id', null)->orWhere('flash_sale_product_id', null)->whereHas('productDetail', function ($q) use ($coupon) {

            $products =  $cart->cartProducts()->whereHas('productDetail', function ($q) use ($coupon) {
                $q->whereHas('product', function ($q) use ($coupon) {
                    $q->whereHas('categoryProducts', function ($q) use ($coupon) {
                        $q->whereNotIn('category_id', $coupon->apply_ids);
                    });
                });
            });
        }
        if ($products) {
            foreach ($products as $product) {
                if ($product->offer_id != null) {
                    if ($product->offer->type == 'buy_x_get_y') {
                        $offerType = $product->offer->buyToGetOffer->discount_type;
                        $offerPrice =   $offerType == 'percentage' ? (($product->offer->buyToGetOffer->discount_amount) *  $product->productDetail->price) / 100   : 0;
                    } else {
                        $offerType = $product->offer->discountOfOffer->discount_type;
                        // info($offerType);
                        $offerPrice =   $offerType == 'value' ? $product->offer->discountOfOffer->discount_amount : (($product->offer->discountOfOffer->max_discount != null ? $product->offer->discountOfOffer->max_discount : $product->offer->discountOfOffer->discount_amount) *  $product->productDetail->price) / 100;
                    }
                    $totalPriceForProduct += ($product->quantity *  ($product->productDetail->price - $offerPrice));
                } else {
                    $totalPriceForProduct += ($product->quantity *     $product->productDetail->price);
                }
            }
        }
        $couponPrice  = $coupon->discount_type == 'value' ? $coupon->discount_amount : ($totalPriceForProduct *  $coupon->discount_amount) / 100;
        $couponPercentage =  $coupon->discount_type == 'value' ? ($totalPriceForProduct - $coupon->discount_amount) / 100 : $coupon->discount_amount;

        if ($coupon->discount_type == 'percentage'  && $coupon->max_discount != null &&     $couponPrice > $coupon->max_discount) {
            $couponPrice  = $coupon->max_discount;
        }
        $data = [
            'couponPrice' =>  $couponPrice,
            'couponPercentage' =>  $couponPercentage,
        ];
        return $data;
    }

    public function  shippingCalculation($address)
    {
        $shippingOn = setting('shipping_on') ? setting('shipping_on') : 'distance';
        $shippingPrice = 0;
        $settingShippingPrice =    setting('shipping_price') ?      setting('shipping_price') : 1;
        switch ($shippingOn) {
            case 'distance':
                $distance  =  point2point_distance($address->lat, $address->lng, setting('lat'), setting('lng'));
                $shippingPrice =      ($distance *     $settingShippingPrice);
                break;
            case 'city':
                if (@$address->city->is_shapping == true) {
                    $distance  =  0;
                    $shippingPrice = @$address->city->shipping_price;
                }
                break;
        }
        return     $shippingPrice;
    }


    public function calculationOfBuyXAndGetY($cartProducts)
    {
        if ($cartProducts) {
            $totalPrice  = 0;
            $sub_total = 0;
            $discount =  0;
            $price = 0;
            foreach ($cartProducts as $item) {
                // dd($item->cartOfferTypes()->count());
                if ($item->cartOfferTypes()->count() > 0) {
                    $cartOfferTypes = $item->cartOfferTypes()->get();
                    foreach ($cartOfferTypes  as $itemOfferType) {
                        if ($itemOfferType->type == 'buy_x') {
                            $price += ($item->productDetail->price *  $itemOfferType->quantity);
                        } elseif ($itemOfferType->type == 'get_y') {
                            $total =  $item->productDetail->price *  $itemOfferType->quantity;
                            $discount += $item->offer->buyToGetOffer->discount_type != 'free_product' ? ($total * $item->offer->buyToGetOffer->discount_amount) / 100 : $total;
                            $price += ($total - $discount);
                        }
                        $totalPrice += (($item->productDetail->price *  $itemOfferType->quantity)  -     $discount);

                        $sub_total +=  ($item->productDetail->price *  $itemOfferType->quantity);
                    }
                }
            }
        }

        return ['totalPrice' => $totalPrice, 'discount' => $discount, 'sub_total' => $sub_total];
    }
    public function calculationItemsPrice($cart, $coupon = null, $address = null, $guestToken = null)
    {
        // info('ppppppppppppppppppp');
        // $cartItems = $cart->cartProducts;
        $couponData =  $coupon != null ? $this->CouponPrice($coupon,  $guestToken) : null;

        $cartProductseWithOfferBuyXGetY = $cart->cartProducts()->whereHas('offer', function ($q) {
            $q->where('type', 'buy_x_get_y');
        })->get();

        $cartItems =  $cartProductseWithOfferBuyXGetY->count() > 0 ?   $cart->cartProducts()->whereNotIn('id', $cartProductseWithOfferBuyXGetY->pluck('id')->toArray())->get() :    $cart->cartProducts;
        //  dd($cartProducts);
        $dataOfOfferXY = null;

        // info('check');
        // info($cartProductseWithOfferBuyXGetY);

        if ($cartProductseWithOfferBuyXGetY != null && !$cartProductseWithOfferBuyXGetY->isEmpty()) {
            // info('check2');
            // info(gettype($cartProductseWithOfferBuyXGetY));
            // dd('ddd');
            $dataOfOfferXY =     $this->calculationOfBuyXAndGetY($cartProductseWithOfferBuyXGetY);
        }


        $sub_total = 0;

        $now = Carbon::now();


        if ($cartItems->count() > 0) {

            foreach ($cartItems as $cartItem) {

                if ($cartItem->flash_sale_product_id  == null && $cartItem->offer_id  == null) {
                    $sub_total += $cartItem->quantity  * $cartItem->productDetail->price;
                }

                if ($cartItem->flash_sale_product_id  != null) {

                    // info('flash_sale_product_id');
//
                    // new  code for
                    if(auth()->guard('api')->check() && auth()->guard('api')->user() != null && $cartItem->flashSaleProduct) {

                        // $order_products_count = OrderProduct::whereHas('order',function($order) use($cartItem) {
                        //     $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])
                        //     ->whereHas('flashSaleOrders', function ($q) use($cartItem)  {
                        //         $q->where('flash_sale_product_id', $cartItem->flashSaleProduct->id);
                        //     });
                        // })->count();

                        $order_products_count = FlashSaleOrder::whereHas('order',function($order) use($cartItem) {
                            $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']);
                        })->whereHas('flash_sale_product', function ($q) use($cartItem) {
                            $q->where('id', $cartItem->flash_sale_product_id);
                        })
                        // ->where('created_at','>=',@$cartItem->flashSaleProduct->flashSale->start_at)->where('created_at','<=',@$cartItem->flashSaleProduct->flashSale->end_at)
                        ->count();

                    } else {
                        $order_products_count = 0;
                    }
                    // new  code for

                    $flashSale = $cartItem->flashSaleProduct->where(\DB::raw('quantity - sold'), '>=', 0)->whereHas('flashSale', function ($q) use ($now) {
                        if (request()->type == 'later') {
                            $q->whereDate('start_at', '>',   now());
                            $q->where('is_active', true);
                        } else {
                            $q->whereDate('start_at', '<=',  $now);
                            $q->whereDate('end_at', '>=',  $now);
                            $q->where('is_active', true);
                        }
                    })->first();
                        // info($flashSale);
                    // if($cartItem->flashSaleProduct && ! ($cartItem->flashSaleProduct->quantity_for_user <= $order_products_count)) {
                    //     $sub_total += $cartItem->quantity  * $cartItem->flashSaleProduct->price_after;
                    // } else {
                    //     $sub_total += $cartItem->quantity  * $cartItem->productDetail->price;
                    // }

                    if($flashSale && $cartItem->flashSaleProduct && ! ($cartItem->flashSaleProduct->quantity_for_user <= $order_products_count)) {
                        $sub_total += $cartItem->quantity  * $cartItem->flashSaleProduct->price_after;
                        // info('ok id ' .$flashSale->id);
                        // info('ok sub_total ' .$sub_total);
                        // info('ok quantity ' .$cartItem->quantity);
                        // info('ok price_after ' .$flashSale->price_after);
                    } else {
                        $sub_total += $cartItem->quantity  * $cartItem->productDetail->price;
                        // info('not ok ' .$sub_total);

                    }
                }



                if ($cartItem->offer_id  != null) {


                    $offer = Offer::whereIn('display_platform',  ['website','both'])->where('id',$cartItem->offer_id)->where('is_active', true)->where('remain_use', '>', 0)->where('start_at', '<=',  $now)->where('end_at', '>=',  $now)->first();

                    if($offer != null) {
                        if($offer->discountOfOffer->discount_type == 'value') {
                            $offerPrice =  $offer->discountOfOffer->discount_amount;
                        } else {
                            $offerPrice = ($offer->discountOfOffer->discount_amount *  $cartItem->productDetail->price) / 100;
                        }
                    } else {
                        $offerPrice = 0;
                    }

                    // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->count())) {
                    // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= OrderProduct::whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count())) {
                    // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id)->where('created_at','>=',$offer->start_at)->where('created_at','<=',$offer->end_at); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count())) {
                    if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count())) {
                                $offerPrice = 0;
                    }
                    if($cartItem->productDetail->price > $offerPrice) {
                        $productPriceAfterDiscount = $cartItem->productDetail->price - $offerPrice;

                    } else {
                        $productPriceAfterDiscount = 0;

                    }

                    // $offerPrice = $offer->discountOfOffer->discount_type == 'value' ? $offer->discountOfOffer->discount_amount : ($offer->discountOfOffer->discount_amount *  $cartItem->productDetail->price) / 100;
                    // $productPriceAfterDiscount =   $cartItem->productDetail->price > $offerPrice ? $cartItem->productDetail->price - $offerPrice : 0;
                    $sub_total += $cartItem->quantity  *  $productPriceAfterDiscount;
                }

            }
        }

        $shippingPrice  = 0;

        if ($address != null) {
            $shippingPrice   =  $this->shippingCalculation($address);
        }

        if ($coupon != null && $coupon->addtion_options == 'free_shipping') {
            $shippingPrice = 0;
        }
        // test it
        $total_product_for_discount_shipping = setting('total_product_for_discount_shipping') ? setting('total_product_for_discount_shipping') : 0;

        if ($total_product_for_discount_shipping  != 0 && $sub_total >= $total_product_for_discount_shipping) {
            $shippingPrice = 0;
        }

        // $sub_total = $sub_total - ($coupon != null ?  $couponData['couponPrice'] : 0);

        $calc_sub_total = $sub_total;

        if($coupon != null) {

            $calc_sub_total = ($couponData['couponPrice'] < $calc_sub_total) ?  ($calc_sub_total - $couponData['couponPrice']) : 0; 

        } 

        $vatData =  $this->vatPrice($dataOfOfferXY != null ? ($calc_sub_total + $dataOfOfferXY['sub_total']) : $calc_sub_total);

        $total = 0.0;

        // if ($dataOfOfferXY != null) {
        //     $total =  $coupon != null  ? $couponData['couponPrice'] > ($dataOfOfferXY['totalPrice'] + $sub_total) ? (0 +  $shippingPrice + $vatData['vat_price']) : ((($dataOfOfferXY['totalPrice'] + $sub_total) - $couponData['couponPrice']) +         $shippingPrice + $vatData['vat_price']) :         $shippingPrice + $sub_total + $dataOfOfferXY['totalPrice'] + $vatData['vat_price'];
        // } else {
        //     $total = $coupon != null  ? $couponData['couponPrice']  > $sub_total  ? (0 +   $shippingPrice + $vatData['vat_price']) : (($sub_total - $couponData['couponPrice']) +         $shippingPrice + $vatData['vat_price']) :         $shippingPrice + $sub_total + $vatData['vat_price'];
        // }

        // info('your sub total is : '.$sub_total);

        if ($dataOfOfferXY != null) {

            // info($dataOfOfferXY);

            // $total =  $coupon != null  ? $couponData['couponPrice'] > ($dataOfOfferXY['totalPrice'] + $sub_total) ? (0 +  $shippingPrice + $vatData['vat_price']) : ((($dataOfOfferXY['totalPrice'] + $sub_total) ) + $shippingPrice + $vatData['vat_price']) :         $shippingPrice + $sub_total + $dataOfOfferXY['totalPrice'] + $vatData['vat_price'];

            // $total =  $coupon != null  ? $couponData['couponPrice'] > ($dataOfOfferXY['totalPrice'] + $sub_total) ? (0 +  $shippingPrice + $vatData['vat_price']) : ((($dataOfOfferXY['totalPrice'] + $sub_total) ) + $shippingPrice + $vatData['vat_price']) :         $shippingPrice + $sub_total + $dataOfOfferXY['totalPrice'] + $vatData['vat_price'];
       
            if($coupon != null) {

                if($couponData['couponPrice'] > ($dataOfOfferXY['totalPrice'] + $calc_sub_total)) {
                    $total =  (0 +  $shippingPrice + $vatData['vat_price']);
                } else {
                    $total = ((($dataOfOfferXY['totalPrice'] + $calc_sub_total) ) + $shippingPrice + $vatData['vat_price']);
                }

            } else {
                $total = $shippingPrice + $calc_sub_total + $dataOfOfferXY['totalPrice'] + $vatData['vat_price'];
            }
            
        } else {
            // $total = $coupon != null  ? $couponData['couponPrice']  > $sub_total  ? (0 +   $shippingPrice + $vatData['vat_price']) : (($sub_total ) + $shippingPrice + $vatData['vat_price']) :         $shippingPrice + $sub_total + $vatData['vat_price'];
            // info('my sub total is '.$sub_total);
            $total = $shippingPrice + $calc_sub_total + $vatData['vat_price'];
        }

        // info('new');
        // info($dataOfOfferXY != null ? $sub_total + $dataOfOfferXY['sub_total'] : $sub_total);

        $allPrice = [
            'sub_total' =>   $dataOfOfferXY != null ? $sub_total + $dataOfOfferXY['sub_total'] : $sub_total,
            'vat' => $vatData['vat_price'],
            'coupon_price' => $coupon != null ?  $couponData['couponPrice'] : 0,
            'shipping' =>      $shippingPrice,
            'total' =>    $total,

        ];
        return $allPrice;
    }

    public function offerbuyXGetY($offerID, $cartProduct, $productDetailID, $cart, $quantity, $count = null, $cartProductQuantity = null)
    {
        $offer = Offer::findOrFail($offerID);
        // dd('quantity' , $quantity);
        $countOfBuy = CartOfferType::where(['offer_id' => $offer->id, 'cart_id' => $cart->id, 'type' => 'buy_x'])->sum('quantity');
        $countOfGet = CartOfferType::where(['offer_id' => $offer->id, 'cart_id' => $cart->id, 'type' => 'get_y'])->sum('quantity');
        if ($countOfBuy < $offer->buyToGetOffer->buy_quantity     && in_array($productDetailID, $offer->buyToGetOffer->buy_apply_ids) == true) {

            if ($quantity > $offer->buyToGetOffer->buy_quantity) {
                CartOfferType::updateOrCreate([
                    'offer_id' => $offer->id,
                    'cart_id' => $cart->id,
                    'type' => 'buy_x',
                    'product_detail_id' => $productDetailID,
                    'cart_product_id' => $cartProduct->id
                ], [
                    'offer_id' => $offer->id,
                    'cart_id' => $cart->id,
                    'type' => 'buy_x',
                    'product_detail_id' => $productDetailID,
                    'cart_product_id' => $cartProduct->id,

                ])->increment('quantity', $offer->buyToGetOffer->buy_quantity);

                if (($quantity - $offer->buyToGetOffer->buy_quantity) > $offer->buyToGetOffer->get_quantity) {
                    CartOfferType::updateOrCreate([
                        'offer_id' => $offer->id,
                        'cart_id' => $cart->id,
                        'type' => 'get_y',
                        'product_detail_id' => $productDetailID,
                        'cart_product_id' => $cartProduct->id
                    ], [
                        'offer_id' => $offer->id,
                        'cart_id' => $cart->id,
                        'type' => 'get_y',
                        'product_detail_id' => $productDetailID,
                        'cart_product_id' => $cartProduct->id,

                    ])->increment('quantity', $offer->buyToGetOffer->get_quantity);
                    $quantityRemoveFromCartProducts = ($quantity - $offer->buyToGetOffer->buy_quantity) - $offer->buyToGetOffer->get_quantity;
                    $cartProduct->decrement('quantity', $quantityRemoveFromCartProducts);
                    // new
                    CartProduct::create([
                        'cart_id' =>  $cartProduct->cart_id,
                        'quantity' =>  $quantityRemoveFromCartProducts,
                        'product_id' => $cartProduct->product_id,
                        'product_detail_id' => $cartProduct->product_detail_id,

                    ]);
                } else {
                    CartOfferType::updateOrCreate([
                        'offer_id' => $offer->id,
                        'cart_id' => $cart->id,
                        'type' => 'get_y',
                        'product_detail_id' => $productDetailID,
                        'cart_product_id' => $cartProduct->id
                    ], [
                        'offer_id' => $offer->id,
                        'cart_id' => $cart->id,
                        'type' => 'get_y',
                        'product_detail_id' => $productDetailID,
                        'cart_product_id' => $cartProduct->id,

                    ])->increment('quantity', ($quantity - $offer->buyToGetOffer->buy_quantity));
                }
            } else {

                CartOfferType::updateOrCreate([
                    'offer_id' => $offer->id,
                    'cart_id' => $cart->id,
                    'type' => 'buy_x',
                    'product_detail_id' => $productDetailID,
                    'cart_product_id' => $cartProduct->id
                ], [
                    'offer_id' => $offer->id,
                    'cart_id' => $cart->id,
                    'type' => 'buy_x',
                    'product_detail_id' => $productDetailID,
                    'cart_product_id' => $cartProduct->id,

                ])->increment('quantity', $quantity);
            }
        } elseif (CartOfferType::where(['offer_id' => $offer->id, 'cart_id' => $cart->id, 'type' => 'buy_x'])->sum('quantity') >= $offer->buyToGetOffer->buy_quantity  && $countOfGet < $offer->buyToGetOffer->get_quantity   && in_array($productDetailID, $offer->buyToGetOffer->get_apply_ids) == true) {
            if ($quantity <= $offer->buyToGetOffer->get_quantity) {

                CartOfferType::updateOrCreate([
                    'offer_id' => $offer->id,
                    'cart_id' => $cart->id,
                    'type' => 'get_y',
                    'product_detail_id' => $productDetailID,
                    'cart_product_id' => $cartProduct->id
                ], [
                    'offer_id' => $offer->id,
                    'cart_id' => $cart->id,
                    'type' => 'get_y',
                    'product_detail_id' => $productDetailID,
                    'cart_product_id' => $cartProduct->id,

                ])->increment('quantity', $quantity);
            } else {
                CartOfferType::updateOrCreate([
                    'offer_id' => $offer->id,
                    'cart_id' => $cart->id,
                    'type' => 'get_y',
                    'product_detail_id' => $productDetailID,
                    'cart_product_id' => $cartProduct->id
                ], [
                    'offer_id' => $offer->id,
                    'cart_id' => $cart->id,
                    'type' => 'get_y',
                    'product_detail_id' => $productDetailID,
                    'cart_product_id' => $cartProduct->id,

                ])->increment('quantity', $offer->buyToGetOffer->get_quantity);
                // here
                $cartProduct->update(['quantity' =>  $offer->buyToGetOffer->get_quantity]);
                CartProduct::updateOrCreate([
                    'cart_id' =>  $cartProduct->cart_id,
                    // 'quantity' => ( $count - $cartProductQuantity ) ,
                    'product_id' => $cartProduct->product_id,
                    'product_detail_id' => $cartProduct->product_detail_id,
                    'offer_id' => null,
                ], [
                    'cart_id' =>  $cartProduct->cart_id,
                    // 'quantity' => ( $count - $cartProductQuantity ) ,
                    'product_id' => $cartProduct->product_id,
                    'product_detail_id' => $cartProduct->product_detail_id,
                    'offer_id' => null,

                ])->increment('quantity', ($quantity - $offer->buyToGetOffer->get_quantity));
            }
        } else {
            // dd('khlood', $cart->id,$cartProduct->quantity - $quantity);

            // if ($cartProduct->quantity == 1) {

            // dd('kh',$cart->cartProducts()->count()  );
            if ($cart->cartProducts()->count() == 1) {
                // $cart->delete();


                CartProduct::create([
                    'cart_id' =>  $cartProduct->cart_id,
                    'quantity' =>  $quantity,
                    'product_id' => $cartProduct->product_id,
                    'product_detail_id' => $cartProduct->product_detail_id,

                ]);

                // CartOfferType::where([
                //     'cart_id' => $cart->id,
                //     'cart_product_id' => $cartProduct->id,
                //     'offer_id' => $offer->id,
                // ])->delete();
                // $cart->cartProducts()->update(['offer_id' => null]);



            } else {
                if ($quantity == 0) {
                    $cartProduct->update(['quantity' => $cartProductQuantity]);
                    // CartProduct::create([
                    //     'cart_id' =>  $cartProduct->cart_id,
                    //     'quantity' =>  $count,
                    //     'product_id' => $cartProduct->product_id,
                    //     'product_detail_id' => $cartProduct->product_detail_id,

                    //  ]);
                } else {
                    // dd('khlood', $cartProduct, $cartProductQuantity, $count);
                    // if ($cartProductQuantity > $count) {

                    $cartProduct->update(['quantity' => $cartProductQuantity]);
                    CartProduct::updateOrCreate([
                        'cart_id' =>  $cartProduct->cart_id,
                        // 'quantity' => ( $count - $cartProductQuantity ) ,
                        'product_id' => $cartProduct->product_id,
                        'product_detail_id' => $cartProduct->product_detail_id,
                        'offer_id' => null,
                    ], [
                        'cart_id' =>  $cartProduct->cart_id,
                        // 'quantity' => ( $count - $cartProductQuantity ) ,
                        'product_id' => $cartProduct->product_id,
                        'product_detail_id' => $cartProduct->product_detail_id,
                        'offer_id' => null,

                    ])->increment('quantity', ($count - $cartProductQuantity));
                    // } else {
                    //     $removeCount = $cartProductQuantity - $count;

                    //     $cartOffers = CartOfferType::Where(['cart_id' => $cartProduct->cart_id, 'product_id' => $cartProduct->product_id,  'offer_id' => $offer->id , 'type' =>'get_y'])->count();
                    //     $cartOffersBuy = CartOfferType::Where(['cart_id' => $cartProduct->cart_id, 'product_id' => $cartProduct->product_id,  'offer_id' => $offer->id , 'type' =>'buy_x'])->count();

                    //     if($cartOffers == $removeCount){
                    //         CartOfferType::Where(['cart_id' => $cartProduct->cart_id, 'product_id' => $cartProduct->product_id,  'offer_id' => $offer->id , 'type' =>'get_y'])->delete();
                    //     }elseif($cartOffers > $removeCount){

                    //         CartOfferType::Where(['cart_id' => $cartProduct->cart_id, 'product_id' => $cartProduct->product_id,  'offer_id' => $offer->id , 'type' =>'get_y'])->decrement('quantity' , $count);

                    //     }elseif(){

                    //     }
                    // }
                }
                // dd($cartProduct->quantity   ,  $quantity);
                // $cartProduct->delete();
                // CartProduct::create([
                //     'cart_id' =>  $cartProduct->cart_id,
                //     'quantity' =>  $quantity,
                //     'product_id' => $cartProduct->product_id,
                //     'product_detail_id' => $cartProduct->product_detail_id,

                //  ]);
                // $cartProduct->decrement('quantity', $quantity);
            }

            // } else {

            // dd('khlood', $cart->id,$cartProduct->quantity - $quantity);
            // $cartProduct->decrement('quantity', $quantity);
            //new code

            // CartProduct::create([
            //    'cart_id' =>  $cartProduct->cart_id,
            //    'quantity' =>  $quantity,
            //    'product_id' => $cartProduct->product_id,
            //    'product_detail_id' => $cartProduct->product_detail_id,

            // ]);

            // }
            // return error to prevent add this offer more
            //  return   $this->responceForCart(trans('app.messages.offer_is_invalid'), null, request()->guest_token,  request()->lat, request()->lng);

        }
    }



    public function useNewCart($request)
    {
        if ($cart = ModelsCart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() != null) {
            $this->useCart($request);
        } else {
            $cart = ModelsCart::create(['user_id' => auth('api')->id()]);

            $productDetailsRow = ProductDetails::find($request->product_detail_id);

            // $arr1 = ['product_id' => $productDetailsRow != null ? $productDetailsRow->product_id : null];
            $arr = array_except($request->validated(), ['guest_token', 'user_id']);
            $arr['product_id'] = $productDetailsRow != null ? $productDetailsRow->product_id : null;

            $cartProduct =  $cart->cartProducts()->create($arr);
            // new code for offer
            if ($request->offer_id != null  && Offer::findOrFail($request->offer_id) && Offer::findOrFail($request->offer_id)->type == 'buy_x_get_y') {
                $this->offerbuyXGetY($request->offer_id, $cartProduct, $request->product_detail_id, $cart, $request->quantity);
            }
        }
        return true;
    }
    public function guestNewCart($request)
    {
        if ($cart = ModelsCart::where(['guest_token' => $request->guest_token, 'user_id' => null])->first() != null) {
            $this->useGuest($request);
        } else {
            $cart = ModelsCart::create(['guest_token' => $request->guest_token]);

            $productDetailsRow = ProductDetails::find($request->product_detail_id);

            $arr = array_except($request->validated(), ['guest_token', 'user_id']);
            $arr['product_id'] = $productDetailsRow != null ? $productDetailsRow->product_id : null;

            $cartProduct =    $cart->cartProducts()->create($arr);
            // new code for offer
            if ($request->offer_id != null  && Offer::findOrFail($request->offer_id) && Offer::findOrFail($request->offer_id)->type == 'buy_x_get_y') {
                $this->offerbuyXGetY($request->offer_id, $cartProduct, $request->product_detail_id, $cart, $request->quantity);
            }
        }
        return true;
    }
    public function useCart($request)
    {
        $cart = $cart = ModelsCart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first();
        $cartProduct   = CartProduct::where([
            'cart_id' => $cart->id,
            'product_detail_id' => $request->product_detail_id,
            'offer_id' => isset($request->offer_id) ? $request->offer_id : null,
            'flash_sale_product_id' => isset($request->flash_sale_product_id) ? $request->flash_sale_product_id : null,
        ])->first();
        $quantity = $cartProduct  != null ? $cartProduct->quantity : 0;

        $productDetailsRow = ProductDetails::find($request->product_detail_id);

        $cartProduct =    $cart->cartProducts()->updateOrCreate([
            // 'product_id' => $productDetailsRow != null ? $productDetailsRow->product_id : null,
            'product_detail_id' => $request->product_detail_id,
            'offer_id' => isset($request->offer_id) ? $request->offer_id : null,
            'flash_sale_product_id' => isset($request->flash_sale_product_id) ? $request->flash_sale_product_id : null,

        ], [
            'product_id' => $productDetailsRow != null ? $productDetailsRow->product_id : null,
            'product_detail_id' => $request->product_detail_id,
            'offer_id' => isset($request->offer_id) ? $request->offer_id : null,
            'flash_sale_product_id' => isset($request->flash_sale_product_id) ? $request->flash_sale_product_id : null,
            'quantity' =>   $quantity + $request->quantity,
        ]);
        // new code for offer
        if ($request->offer_id != null && Offer::findOrFail($request->offer_id) && Offer::findOrFail($request->offer_id)->type == 'buy_x_get_y') {
            $this->offerbuyXGetY($request->offer_id, $cartProduct, $request->product_detail_id, $cart, $request->quantity, $request->quantity, $quantity);
        }
        return true;
    }

    public function useGuest($request)
    {
        $cart = ModelsCart::where(['guest_token' => $request->guest_token, 'user_id' => null])->firstOrFail();
        $cartProduct   = CartProduct::where([
            'cart_id' => $cart->id,
            'product_detail_id' => $request->product_detail_id,
            'offer_id' => isset($request->offer_id) ? $request->offer_id : null,
            'flash_sale_product_id' => isset($request->flash_sale_product_id) ? $request->flash_sale_product_id : null,
        ])->first();
        $quantity = $cartProduct  != null ? $cartProduct->quantity : 0;
        $cartProduct =     $cart->cartProducts()->updateOrCreate([
            'product_detail_id' => $request->product_detail_id,
            'offer_id' => isset($request->offer_id) ? $request->offer_id : null,
            'flash_sale_product_id' => isset($request->flash_sale_product_id) ? $request->flash_sale_product_id : null,

        ], [
            'product_detail_id' => $request->product_detail_id,
            'offer_id' => isset($request->offer_id) ? $request->offer_id : null,
            'flash_sale_product_id' => isset($request->flash_sale_product_id) ? $request->flash_sale_product_id : null,
            'quantity' =>   $quantity + $request->quantity,
        ]);

        // new code for offer
        if ($request->offer_id != null && Offer::findOrFail($request->offer_id) && Offer::findOrFail($request->offer_id)->type == 'buy_x_get_y') {
            $this->offerbuyXGetY($request->offer_id, $cartProduct, $request->product_detail_id, $cart, $request->quantity);
        }
        return true;
    }

    public function  addGuestDataToUserCart($request)
    {
        $guestCart = ModelsCart::where(['guest_token' => $request['guest_token'], 'user_id' => null])->first();
        $userCart = ModelsCart::firstOrCreate([
            'user_id' => auth('api')->id(),
            'guest_token' => null,
        ], [
            'user_id' => auth('api')->id(),
            'guest_token' => null,
        ]);
        if ($guestCart) {
            // $userCartProduct = $userCart->cartProducts->get();
            $guestCartProducts =  $guestCart->cartProducts()->get();
            if ($guestCartProducts) {
                foreach ($guestCartProducts as $guestCartProduct) {
                    $userCartData =     $userCart->cartProducts()->updateOrCreate(
                        [
                            'product_id' => $guestCartProduct->product_id,
                            'product_detail_id' => $guestCartProduct->product_detail_id,
                            'offer_id' => $guestCartProduct->offer_id,
                            'flash_sale_product_id' => $guestCartProduct->flash_sale_product_id,
                        ],
                        [
                            'product_id' => $guestCartProduct->product_id,
                            'product_detail_id' => $guestCartProduct->product_detail_id,
                            'offer_id' => $guestCartProduct->offer_id,
                            'flash_sale_product_id' => $guestCartProduct->flash_sale_product_id,
                        ]
                    );
                    $userCartData->update([
                        'quantity' => $guestCartProduct->quantity  +  $userCartData->quantity,

                    ]);

                    // new code for offer
                    if ($guestCartProduct->offer_id != null  && Offer::findOrFail($guestCartProduct->offer_id) && Offer::findOrFail($guestCartProduct->offer_id)->type == 'buy_x_get_y') {
                        $this->offerbuyXGetY($guestCartProduct->offer_id, $userCartData, $guestCartProduct->product_detail_id, $userCart, $userCartData->quantity);
                    }

                    $guestCartProduct->delete();
                }
            }
            $guestCart->delete();
        }
        return true;
    }

    public function  changeGuestToUser($request)
    {
        $cart = Cart::where(['guest_token' => $request->guest_token, 'user_id' => null])->first();
        if ($cart) {
            $cart->update([
                'user_id' => auth('api')->id(),
                'guest_token' => null
            ]);
        }
        return true;
    }
}
