<?php

namespace App\Traits;


use App\Models\{Company, Coupon, FlashSaleOrder, Order, Offer, FlashSaleProduct, OfferOrder, OrderCoupon, OrderPriceDetail, ProductDetails};
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


trait OrderOperation
{


    public function calculationItemsPrice($cart, $coupon = null, $address = null)
    {
        $now = Carbon::now();

        $cartItems = $cart->cartProducts;

        $couponData =  $coupon != null ? $this->CouponPrice($coupon->id) : null;

        $sub_total = 0;

        if ($cartItems) {

            foreach ($cartItems as $cartItem) {

                if ($cartItem->offer_id  != null) {

                    $offer = Offer::find($cartItem->offer_id);

                    if($offer != null && $offer->start_at <= $now && $offer->end_at >= $now && $offer->max_use > OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count()) {

                        // $offerPrice = $offer->discount_type == 'value' ? $offer->discount_amount : ($offer->discount_amount *  $cartItem->productDetail->price) / 100;
                        // $productPriceAfterDiscount =   $cartItem->productDetail->price - $offerPrice;
                        // $sub_total += $cartItem->quantity  *  $productPriceAfterDiscount;

                        if ($offer->type == 'fix_amount' || $offer->type == 'percentage') {
                
                            $offerPrice = $offer->discountOfOffer->discount_type == 'value' ? $offer->discountOfOffer->discount_amount : (($offer->discountOfOffer->max_discount != null ? $offer->discountOfOffer->max_discount : $offer->discountOfOffer->discount_amount) *  $cartItem->productDetail->price) / 100;
                
                            $productPriceAfterDiscount =   $cartItem->productDetail->price - $offerPrice;
                            $sub_total += $cartItem->quantity  *  $productPriceAfterDiscount;                          
                
                            // info('sub_total is '.$sub_total);
                        }

                    } else {

                        $sub_total += $cartItem->quantity  *  $cartItem->productDetail->price;

                        // info('sub_total is '.$sub_total);

                    }


                } elseif ($cartItem->flash_sale_product_id  != null) {

                    // $sub_total += $cartItem->quantity  * $cartItem->flashSaleProduct->price_after;

                    $flashSalesProduct = FlashSaleProduct::where('id',$cartItem->flash_sale_product_id)->whereHas('flashSale',function($flashSale) use($now) {
                        $flashSale->where('start_at', '<=',  $now)->where('end_at', '>=',  $now);
                    })->first();

                    $order_products_count = FlashSaleOrder::whereHas('order',function($order) {
                        $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']);
                    })->whereHas('flash_sale_product', function ($q) use($flashSalesProduct) {
                        $q->where('id', $flashSalesProduct->id);
                    })
                    // ->where('created_at','>=',$flashSalesProduct->flashSale->start_at)->where('created_at','<=',$flashSalesProduct->end_at)
                    ->count();

                    if($flashSalesProduct && ($flashSalesProduct != null && $flashSalesProduct->quantity_for_user > $order_products_count)) {

                       $sub_total += $cartItem->quantity  * $cartItem->flashSaleProduct->price_after;

                    } else {

                        $sub_total += $cartItem->quantity  * $cartItem->productDetail->price;

                    }

                } else {
                    $sub_total += $cartItem->quantity  * $cartItem->productDetail->price;
                }
                
            }
        }

        $shippingPrice  = null;

        if ($address != null) {
            $shippingPrice  = $this->shippingCalculation($address);
        }

        $couponDataPrice = 0;
        $shippingDataPrice = 0;

        if($couponData != null && array_key_exists("couponPrice",$couponData)) {
            $couponDataPrice = $couponData['couponPrice'];
        }

        if($shippingPrice != null && array_key_exists("shippingPrice",$shippingPrice)) {
            $shippingDataPrice = $shippingPrice['shippingPrice'];
        }

        if($coupon != null) {
            if($sub_total > $couponDataPrice) {
                $calcTotal = ($sub_total - $couponDataPrice) + $shippingDataPrice;
            } else {
                $calcTotal =  $shippingDataPrice;
            }
        } else {
            $calcTotal = $shippingDataPrice + $sub_total;
        }

        $allPrice = [
            'sub_total' => $sub_total,
            'vat' => 0,
            'coupon_price' => $coupon != null ? $couponDataPrice : 0,
            'shipping' => $shippingDataPrice,
            'total' =>   $calcTotal ,

        ];
        return $allPrice;
    }



    public function CheckCouponIsValide($coupon, $user = null, $cart = null)
    {
        $message = null;

        $now = Carbon::now();

        if ($coupon->is_active == 0) {
            $message = trans('app.messages.coupon.not_active');
        }

        if ($coupon && $coupon->is_active && $coupon->max_used_num <= $coupon->num_of_used) {
            $message = trans('app.messages.coupon.coupon_is_expired');
        }
        $numOfUsed = Order::where(['user_id' => auth('api')->id()])->whereHas('orderCoupon', function ($q) use ($coupon) {
            $q->where(['coupon_id' => $coupon->id]);
        })->count();

        if ($coupon && $coupon->is_active && isset($coupon->max_used_for_user) && $coupon->max_used_for_user  <=   $numOfUsed) {
            $message = trans('app.messages.coupon.You_have_exceeded_number_of_times_you_used_coupon');
        }
        if ($coupon && $coupon->is_active && !($coupon->start_at <= $now && $coupon->end_at >= $now)) {
            $message = trans('app.messages.coupon.expired_date');
        }
        // if ($cart != null && $cart->cartOfferTypes()->whereHas('offer', function ($q) {
        //     $q->where('type', 'buy_x_get_y');
        // })->first() != null) {

        //     $message = trans('app.messages.coupon.coupon_cannot_be_used_with_this_offer');
        // }


        if ($cart != null && $cart->cartProducts()->where('offer_id',  '!=', null)->count()  > 0) {
            $offers =  $cart->cartProducts()->where('offer_id',  '!=', null)->get();
            foreach ($offers as $offer) {
                if ($offer->is_active && $offer->offer->is_with_coupon  == 0) {

                    $message = trans('app.messages.coupon.coupon_cannot_be_used_with_this_offer');
                }
            }
        }

        return $message;
    }



    public function buy_x_get_yForOffer($cart, $offer)
    {
        $message  = null;
        if ($offer->buyToGetOffer->buy_apply_on == 'special_products') {
            // $cartProductCount = $cart->cartProducts()->where('offer_id', $offer->id)->whereIn('product_detail_id', $offer->buyToGetOffer->buy_apply_ids)->sum('quantity');
            $cartProductCount  = $cart->cartOfferTypes()->where('offer_id', $offer->id)->where('type', 'buy_x')->sum('quantity');
            if ($cartProductCount >  $offer->buyToGetOffer->buy_quantity) {
                // return error
                $message = trans('app.messages.offer_is_invalid');
            }
        } elseif ($offer->buyToGetOffer != null && $offer->buyToGetOffer->buy_apply_on == 'special_categories' && is_array($offer->buyToGetOffer->buy_apply_on) && ! empty($offer->buyToGetOffer->buy_apply_on)) {

            $cartProductCount = $cart->cartOfferTypes()->where('offer_id', $offer->id)
                ->where('type', 'buy_x')
                ->whereHas('productDetail', function ($q) use ($offer) {
                    $q->whereHas('product', function ($q) use ($offer) {
                        $q->whereHas('categoryProducts', function ($q) use ($offer) {
                            $q->whereIn('category_id', $offer->buyToGetOffer->buy_apply_on);
                        });
                    });
                })
                ->sum('quantity');
            // $cartProductCount = $cart->cartProducts()->where('offer_id', $offer->id)
            //     ->whereHas('productDetail', function ($q) use ($offer) {
            //         $q->whereHas('product', function ($q) use ($offer) {
            //             $q->whereHas('categoryProducts', function ($q) use ($offer) {
            //                 $q->whereIN('category_id', $offer->buyToGetOffer->buy_apply_on);
            //             });
            //         });
            //     })
            //     ->sum('quantity');
            if ($cartProductCount >  $offer->buyToGetOffer->buy_quantity) {
                $message = trans('app.messages.offer_is_invalid');
            }
        }
        // elseif ($offer->buyToGetOffer->buy_apply_on == 'all') {
        //     $cartProductCount = $cart->cartProducts()->where('offer_id', $offer->id)->sum('quantity');
        //     if ($cartProductCount >  $offer->buyToGetOffer->buy_quantity) {
        //         $message = trans('app.messages.offer_is_invalid');
        //     }
        // }
        return   $message != null ? $message  : true;
    }



    public function discountOfOffer($cart, $offer, $payment)
    {
        $message = null;
        if ($offer->discountOfOffer->apply_on == 'special_products' && $offer->discountOfOffer->min_type != null) {
            if ($offer->discountOfOffer->min_type == 'quantity_of_products') {
                $cartProducts = $cart->cartProducts()->where('offer_id', $offer->id)->whereIn('product_detail_id', $offer->discountOfOffer->apply_ids)->count();

                // $cartProducts = $cart->cartProducts()->whereIn('product_detail_id', $offer->discountOfOffer->apply_ids)->count();
                if ($cartProducts < $offer->discountOfOffer->min_value) {
                    $message = trans('app.messages.offer_is_invalid');
                }
            } else {
                // $cartProducts = $cart->cartProducts()->whereIn('product_detail_id', $offer->discountOfOffer->apply_ids)->pluck('product_detail_id')->toArray();

                $cartProducts = $cart->cartProducts()->where('offer_id', $offer->id)->whereIn('product_detail_id', $offer->discountOfOffer->apply_ids)->pluck('product_detail_id')->toArray();
                $sumOfProductsInCart = ProductDetails::whereIn('id',    $cartProducts)->sum('price');

                if ($sumOfProductsInCart < $offer->discountOfOffer->min_value) {
                    $message = trans('app.messages.offer_is_invalid');
                }
            }
        } elseif ($offer->discountOfOffer != null && $offer->discountOfOffer->apply_on == 'special_categories' && is_array($offer->discountOfOffer->buy_apply_on) && ! empty($offer->discountOfOffer->buy_apply_on)) {

            if ($offer->min_type == 'quantity_of_products') {
                $cartProducts = $cart->cartProducts()->where('offer_id', $offer->id)
                    ->whereHas('productDetail', function ($q) use ($offer) {
                        $q->whereHas('product', function ($q) use ($offer) {
                            $q->whereHas('categoryProducts', function ($q) use ($offer) {
                                $q->whereIN('category_id', $offer->discountOfOffer->buy_apply_on);
                            });
                        });
                    })
                    ->count();
                if ($cartProducts < $offer->discountOfOffer->min_value) {
                    $message = trans('app.messages.offer_is_invalid');
                }
            } else {
                $cartProducts = $cart->cartProducts()->where('offer_id', $offer->id)
                    ->whereHas('productDetail', function ($q) use ($offer) {
                        $q->whereHas('product', function ($q) use ($offer) {
                            $q->whereHas('categoryProducts', function ($q) use ($offer) {
                                $q->whereIN('category_id', $offer->discountOfOffer->buy_apply_on);
                            });
                        });
                    })
                    ->pluck('product_detail_id')->toArray();
                $sumOfProductsInCart = ProductDetails::whereIn('id', $cartProducts)->sum('price');
                if ($sumOfProductsInCart < $offer->discountOfOffer->min_value) {
                    $message = trans('app.messages.offer_is_invalid');
                }
            }
        } elseif ($offer->discountOfOffer->apply_on == 'special_payment' && $offer->discountOfOffer->apply_on  != $payment) {
            $message = trans('app.messages.offer_is_invalid');
        } elseif ($offer->discountOfOffer->apply_on == 'all') {
            if ($offer->discountOfOffer->min_type == 'quantity_of_products') {
                $cartProducts = $cart->cartProducts()->where('offer_id', $offer->id)->count();
                if ($cartProducts < $offer->discountOfOffer->min_value) {
                    $message = trans('app.messages.offer_is_invalid');
                }
            } else {
                $cartProducts = $cart->cartProducts()->where('offer_id', $offer->id)->whereIn('product_detail_id', $offer->discountOfOffer->apply_ids)->pluck('product_detail_id')->toArray();
                $sumOfProductsInCart = ProductDetails::whereIn('id', $cartProducts)->sum('price');
                if ($sumOfProductsInCart < $offer->discountOfOffer->min_value) {
                    $message = trans('app.messages.offer_is_invalid');
                }
            }
        }
        return   $message != null ? $message  : null;
    }



    // type : order , cart
    public function  CheckOfferIsValide($type, $offerId = null, $payment = null, $couponCode = null)
    {
        $message = null;
        $now = Carbon::now();

        switch ($type) {
            case 'cart':
                $offer = Offer::find($offerId);

                if ($offer == null) {
                    $message = trans('app.messages.offer_not_exists');
                    return $message != null ? $message : null;
                }
                
                if ($offer->is_active == 0) {
                    $message = trans('app.messages.offer_not_active');
                }

                if ($offer->remain_use <= 0) {
                    $message = trans('app.messages.offer_is_finished');
                }
                if (!($offer->start_at <= $now && $offer->end_at >= $now)) {
                    $message = trans('app.messages.offer_expired_date');
                }
                // if (auth()->guard('api')->user() != null && ($offer->max_use <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->count())) {
                if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count())) {
                        $message = trans('app.messages.You_have_exceeded_number_of_times_you_used_offer');
                }
                if ($offer->is_active && $couponCode != null && $offer->is_with_coupon == 0) {
                    $message = trans('app.messages.coupon.coupon_cannot_be_used_with_this_offer');
                }

                // new validation
                $route = \Request::route()->getPrefix();
                $display_platform =  $offer->display_platform;

                if ($display_platform == 'app'   &&     $route != 'app') {
                    $message = trans('app.messages.this_offer_for_website');
                }
                if ($display_platform == 'website' &&     $route != 'website') {
                    $message = trans('app.messages.this_offer_for_app');
                }

                break;

            case 'order':

                $offers = auth()->guard('api')->user()->cart->cartProducts()->where('offer_id', '!=', null)->get();

                if ($offers) {
                    foreach ($offers as $valid) {
                        $offer =   $valid->offer;
                        if ($offer->is_active == 0) {
                            $message = trans('app.messages.offer_not_active');
                        }
                        if ($offer->remain_use <= 0) {
                            $message = trans('app.messages.offer_is_finished');
                        }
                        if (!($offer->start_at <= $now && $offer->end_at >= $now)) {
                            $message = trans('app.messages.offer_expired_date');
                        }
                        // if (auth()->guard('api')->user() != null && ($offer->max_use <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->count())) {
                        if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count())) {
                            $message = trans('app.messages.You_have_exceeded_number_of_times_you_used_offer');
                        }

                        // new validation
                        $route = \Request::route()->getPrefix();
                        $display_platform =  $offer->display_platform;
                        if ($display_platform == 'app'  &&     $route != 'app') {
                            $message = trans('app.messages.this_offer_for_website');
                        }
                        if ($display_platform == 'website'   &&     $route != 'website') {
                            $message = trans('app.messages.this_offer_for_app');
                        }
                        if ($offer->type == 'buy_x_get_y') {
                            $message =     $this->buy_x_get_yForOffer(auth()->guard('api')->user()->cart, $offer);
                        }
                        if ($offer->is_active && $couponCode != null && $offer->is_with_coupon == 0) {
                            $message = trans('app.messages.coupon.coupon_cannot_be_used_with_this_offer');
                        }
                        if ($offer->type == 'fix_amount' || $offer->type == 'percentage') {
                            $message =    $this->discountOfOffer(auth()->guard('api')->user()->cart, $offer, $payment);
                        }
                    }
                }
                break;
        }

        return $message != null ? $message : null;
    }


    // type : order , cart
    public function CheckFlashSaleIsValide($type, $flashSaleProduct = null)
    {
        $message = null;

        $now = Carbon::now();

        switch ($type) {

            case 'cart':

                $flashSaleProduct = FlashSaleProduct::find($flashSaleProduct);

                if ($flashSaleProduct == null) {
                    $message = trans('app.messages.flashsale_not_exists');
                }

                if ($flashSaleProduct->flashSale->is_active == 0) {
                    $message = trans('app.messages.flashsale_is_not_active');
                }
                if (($flashSaleProduct->quantity  -  $flashSaleProduct->sold) <= 0) {
                    $message = trans('app.messages.quantity_is_finished');
                }

                $order_products_count = FlashSaleOrder::whereHas('order',function($order) {
                    $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']);
                })->whereHas('flash_sale_product', function ($q) use($flashSaleProduct) {
                    $q->where('id', $flashSaleProduct->id);
                })
                // ->where('created_at','>=',$flashSaleProduct->flashSale->start_at)->where('created_at','<=',$flashSaleProduct->flashSale->end_at)
                ->count();

                // if (auth()->guard('api')->user() != null && ($flashSaleProduct->quantity_for_user <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->whereHas('flashSaleOrders', function ($q) use ($flashSaleProduct) {
                //     $q->where('flash_sale_product_id', $flashSaleProduct->id);
                // })->count())) {

                if (auth()->guard('api')->user() != null && ($flashSaleProduct->quantity_for_user <= $order_products_count)) {
                    $message = trans('app.messages.You_have_exceeded_number_of_times_you_used_offer');
                }

                break;

            case 'order':

                $flashSaleProducts = auth()->guard('api')->user()->cart->cartProducts()->where('flash_sale_product_id', '!=', null)->get();

                if ($flashSaleProducts) {
                    foreach ($flashSaleProducts as $valid) {

                        $flashSaleProduct =   $valid->flashSaleProduct;
                        
                        if ($flashSaleProduct->flashSale->is_active == 0) {
                            $message = trans('app.messages.flashsale_id_not_active');
                        }
                        if (($flashSaleProduct->quantity  -  $flashSaleProduct->sold) <= 0) {
                            $message = trans('app.messages.quantity_is_finished');
                        }

                        $order_products_count = FlashSaleOrder::whereHas('order',function($order) {
                            $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']);
                        })->whereHas('flash_sale_product', function ($q) use($flashSaleProduct) {
                            $q->where('id', $flashSaleProduct->id);
                        })
                        // ->where('created_at','>=',$flashSaleProduct->flashSale->start_at)->where('created_at','<=',$flashSaleProduct->flashSale->end_at)
                        ->count();

                        // if (auth()->guard('api')->user() != null && ($flashSaleProduct->quantity_for_user <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->whereHas('flashSaleOrders', function ($q) use ($flashSaleProduct) {
                        //     $q->where('flash_sale_product_id', $flashSaleProduct->id);
                        // })->count())) {

                        if (auth()->guard('api')->user() != null && ($flashSaleProduct->quantity_for_user <= $order_products_count)) {
                            $message = trans('app.messages.You_have_exceeded_number_of_times_you_used_offer');
                        }
                    }
                }
                break;
        }
        return $message;
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


    public function CouponPrice($coupon_id)
    {
        $coupon = Coupon::findOrFail($coupon_id);
        $couponPrice = 0;
        $offerPercentage = 0;
        $totalPriceForProduct  = 0;

        if ($coupon->applly_coupon_on == 'all') {
            // $products = auth()->guard('api')->user()->cart->cartProducts()->where('offer_id', null)->orWhere('flash_sale_product_id', null)->get();
            $products = auth()->guard('api')->user()->cart->cartProducts()->get();

            // foreach ($products as $product) {
            //     $totalPriceForProduct += ($product->quantity *     $product->productDetail->price);
            // }
            // $couponPrice  = $coupon->discount_type == 'value' ? $coupon->discount_amount : ($totalPriceForProduct *  $coupon->discount_amount) / 100;
            // $couponPercentage =  $coupon->discount_type == 'value' ? ($totalPriceForProduct - $coupon->discount_amount) / 100 : $coupon->discount_amount;
        }
        if ($coupon->applly_coupon_on == 'special_products') {
            // $products = auth()->guard('api')->user()->cart->cartProducts()->where('offer_id', null)->orWhere('flash_sale_product_id', null)->whereIn('product_detail_id', $coupon->apply_ids)->get();
            $products = auth()->guard('api')->user()->cart->cartProducts()->whereIn('product_detail_id', $coupon->apply_ids)->get();
        }
        if ($coupon->applly_coupon_on == 'except_products') {
            // $products = auth()->guard('api')->user()->cart->cartProducts()->where('offer_id', null)->orWhere('flash_sale_product_id', null)->whereNotIn('product_detail_id', $coupon->apply_ids)->get();
            $products = auth()->guard('api')->user()->cart->cartProducts()->whereNotIn('product_detail_id', $coupon->apply_ids)->get();
        }
        if ($coupon->applly_coupon_on == 'special_categories') {
            // $products = auth()->guard('api')->user()->cart->cartProducts()->where('offer_id', null)->orWhere('flash_sale_product_id', null)->whereHas('productDetail', function ($q) use ($coupon) {

            $products = auth()->guard('api')->user()->cart->cartProducts()->whereHas('productDetail', function ($q) use ($coupon) {
                $q->whereHas('product', function ($q) use ($coupon) {
                    $q->whereHas('categoryProducts', function ($q) use ($coupon) {
                        $q->whereIn('category_id', $coupon->apply_ids);
                    });
                });
            });
        }
        if ($coupon->applly_coupon_on == 'except_categories') {
            // $products = auth()->guard('api')->user()->cart->cartProducts()->where('offer_id', null)->orWhere('flash_sale_product_id', null)->whereHas('productDetail', function ($q) use ($coupon) {

            $products = auth()->guard('api')->user()->cart->cartProducts()->whereHas('productDetail', function ($q) use ($coupon) {
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
                        $offerPrice =   $offerType == 'value' ? $product->offer->discountOfOffer->discount_amount : (($product->offer->discountOfOffer->max_discount != null ? $product->offer->discountOfOffer->max_discount : $product->offer->discountOfOffer->discount_amount) *  $product->productDetail->price) / 100;
                    }
                    $totalPriceForProduct += ($product->quantity *  ($product->productDetail->price - $offerPrice));
                } else {
                    $totalPriceForProduct += ($product->quantity *     $product->productDetail->price);
                }
            }
        }

        $couponPrice  = $coupon->discount_type == 'value' ? $coupon->discount_amount : ($totalPriceForProduct *  $coupon->discount_amount) / 100;
        $couponPercentage =  $coupon->discount_type == 'value' ? (100 - ($totalPriceForProduct - $coupon->discount_amount)) : $coupon->discount_amount;
        if ($coupon->discount_type == 'percentage'  && $coupon->max_discount != null &&     $couponPrice > $coupon->max_discount) {
            $couponPrice  = $coupon->max_discount;
        }
        $data = [
            'couponPrice' =>  $couponPrice,
            'couponPercentage' =>  $couponPercentage,
            // 'addtion_options' =>  $coupon->addtion_options != null ?$coupon->addtion_options : null,
        ];
        return $data;
    }
    public function offerPrice($offer, $productDetail)
    {
        $data = [];
        $offerPrice = $offer->discount_type == 'value' ? $offer->discount_amount : ($offer->discount_amount *  $productDetail->price) / 100;
        $offerPercentage =  $offer->discount_type == 'value' ? ($productDetail->price - $offer->discount_amount) / 100 : $offer->discount_amount;
        $productPriceAfterDiscount =  $productDetail->price > $offerPrice ? $productDetail->price - $offerPrice : 0;
        $data = [
            'offer_percentage' => $offerPercentage,
            'offer_price' =>   $offerPrice,
            'productPriceAfterDiscount' => $productPriceAfterDiscount,
        ];
        return $data;
    }

    public function newOfferPrice($offer, $productDetail)
    {
        $data = [];
        if ($offer->type == 'fix_amount' || $offer->type == 'percentage') {
            // $offerPrice = $offer->discountOfOffer->discount_type == 'value' ? $offer->discountOfOffer->discount_amount : (($offer->discountOfOffer->max_discount != null ? $offer->discountOfOffer->max_discount : $offer->discountOfOffer->discount_amount) *  $productDetail->price) / 100;

            $offerPrice = $offer->discountOfOffer->discount_type == 'value' ? $offer->discountOfOffer->discount_amount : (($offer->discountOfOffer->max_discount != null ? $offer->discountOfOffer->max_discount : $offer->discountOfOffer->discount_amount) *  $productDetail->price) / 100;

            // if($offer->discountOfOffer->discount_type == 'value') {
            //     $offerPrice = $offer->discountOfOffer->discount_amount;
            // } else {
            //     if($offer->discountOfOffer->max_discount != null) {
            //         $offerPrice = ($offer->discountOfOffer->max_discount *  $productDetail->price) / 100;
            //     } else {
            //         $offerPrice =  ($offer->discountOfOffer->discount_amount *  $productDetail->price) / 100;
            //     }
            // }

            $offerPercentage =  $offer->discountOfOffer->discount_type == 'value' ? ($productDetail->price - $offer->discountOfOffer->discount_amount) / 100 : $offer->discountOfOffer->discount_amount;
            $productPriceAfterDiscount =  $productDetail->price - $offerPrice;
        }
        $data = [
            'offer_percentage' => $offerPercentage,
            'offer_price' =>   $offerPrice,
            'productPriceAfterDiscount' => $productPriceAfterDiscount,
        ];
        return $data;
    }
    public function productHasValues($cartProducts)
    {
        $checkQuantity = true;
        if ($cartProducts) {
            foreach ($cartProducts as $cartProduct) {
                if ($cartProduct->quantity > $cartProduct->productDetail->quantity) {
                    $checkQuantity = false;
                }
                if ($cartProduct->flash_sale_product_id !=  null && ($cartProduct->flashSaleProduct->quantity  -  $cartProduct->flashSaleProduct->sold) < $cartProduct->quantity) {
                    $checkQuantity = false;
                }
                if ($cartProduct->flash_sale_product_id !=  null && $cartProduct->flashSaleProduct->quantity_for_user < auth()->guard('api')->user()->orders()->where('status', 'client_finished')->whereHas('flashSaleOrders', function ($q) use ($cartProduct) {
                    $q->where('flash_sale_product_id', $cartProduct->flash_sale_product_id);
                })->count()) {
                    $checkQuantity = false;
                }
            }
        }
        return   $checkQuantity;
    }

    public function  shippingCalculation($address, $coupon = null)
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
                if ($address->city->is_shapping == true) {
                    $distance  =  0;
                    $shippingPrice = $address->city->shipping_price;
                }
                break;
        }

        $data = [
            'distance'   =>   $distance,
            'shippingPrice' => $coupon != null ? ($coupon->addtion_options == 'free_shipping' ? 0 : $shippingPrice) : $shippingPrice,
        ];
        return       $data;
    }
    public function calculationOfBuyXAndGetY($cartProducts, $order)
    {
        if ($cartProducts) {
            $totalPrice  = 0;
            $price = 0;
            $discount =  0;
            foreach ($cartProducts as $item) {
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
                    }
                }
                $order_product = $order->orderProducts()->create([
                    'quantity' =>  $item->quantity,
                    'product_detail_id' => $item->product_detail_id,
                    'price' => $item->productDetail->price,
                    'total_product_price_before' => $item->productDetail->price *  $item->quantity,
                    'offer_id' => $item->offer_id,
                    'offer_data' => $item->offer_id ? $item->offer->toJson() : null,
                    'offer_percentage' => $item->offer_id ? $item->offer->buyToGetOffer->discount_amount : null,
                    'offer_price' => $item->offer_id ?    $discount  : null,
                    'total_price' => ($item->productDetail->price *  $item->quantity)  -     $discount
                ]);
                $totalPrice +=  $order_product->total_price;
                $order_product->productDetail()->update([
                    'quantity' => ($order_product->productDetail->quantity - $item->quantity)
                ]);
                if ($item->offer_id !=  null) {
                    OfferOrder::updateOrCreate([
                        'user_id' => auth('api')->id(),
                        'offer_id' => $item->offer_id,
                        'order_id' =>   $order->id,
                    ], [
                        'user_id' => auth('api')->id(),
                        'offer_id' => $item->offer_id,
                        'order_id' =>   $order->id,
                    ]);
                }
            }
        }

        return true;
    }
    public function addProduct($request)
    {
        $order =    Order::create($request->validated() + [
            'user_id' => auth('api')->id(),  'order_status_times' => ['pending' => date("Y-m-d H:i")],
        ]);


        $cartProductseWithOfferBuyXGetY = auth()->guard('api')->user()->cart->cartProducts()->whereHas('offer', function ($q) {
            $q->where('type', 'buy_x_get_y');
        })->get();
        $cartProducts =  $cartProductseWithOfferBuyXGetY->count() > 0 ?   auth()->guard('api')->user()->cart->cartProducts()->whereNotIn('id', $cartProductseWithOfferBuyXGetY->pluck('id')->toArray())->get() :    auth()->guard('api')->user()->cart->cartProducts()->get();
        //  dd($cartProducts);
        if ($cartProductseWithOfferBuyXGetY) {
            // dd('ddd');
            $this->calculationOfBuyXAndGetY($cartProductseWithOfferBuyXGetY, $order);
        }

        $now = Carbon::now();

        if ($cartProducts) {

            $totalPrice  = 0;
            $totalDiscount = 0;

            foreach ($cartProducts as $cartProduct) {

                $calc_total_price = 0;

                $offerPrice = null;

                $product_details_row = ProductDetails::find($cartProduct->product_detail_id);

                // $cartProduct->flash_sale_product_id ? $cartProduct->flashSaleProduct->price_after * $cartProduct->quantity : ($cartProduct->offer_id  ? ($offerPrice['productPriceAfterDiscount'] *  $cartProduct->quantity) :    $cartProduct->productDetail->price *  $cartProduct->quantity)

                if ($cartProduct->offer_id != null) {

                    $offer = Offer::find($cartProduct->offer_id);

                    // info($offer->max_use);
                    // info(OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count());

                    if($offer != null && $offer->start_at <= $now && $offer->end_at >= $now && $offer->max_use > OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count()) {

                        $offerPrice = $this->newOfferPrice($cartProduct->offer, $cartProduct->productDetail);

                        $calc_total_price = $offerPrice['productPriceAfterDiscount'] * $cartProduct->quantity;

                        info('first calc');
                        info($calc_total_price);

                    } else {

                        $calc_total_price = $cartProduct->productDetail->price *  $cartProduct->quantity;

                        info('second calc');

                    }

                } elseif($cartProduct->flash_sale_product_id) {

                    $flashSalesProduct = FlashSaleProduct::where('id',$cartProduct->flash_sale_product_id)->whereHas('flashSale',function($flashSale) use($now) {
                        $flashSale->where('start_at', '<=',  $now)->where('end_at', '>=',  $now);
                    })->first();

                    $order_products_count = FlashSaleOrder::whereHas('order',function($order) {
                        $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']);
                    })->whereHas('flash_sale_product', function ($q) use($flashSalesProduct) {
                        $q->where('id', $flashSalesProduct->id);
                    })
                    // ->where('created_at','>=',$flashSalesProduct->flashSale->start_at)->where('created_at','<=',$flashSalesProduct->end_at)
                    ->count();

                    if($flashSalesProduct && ($flashSalesProduct != null && $flashSalesProduct->quantity_for_user > $order_products_count)) {

                        info('third calc');

                        $calc_total_price = $cartProduct->flashSaleProduct->price_after * $cartProduct->quantity;

                    } else {

                        info('four calc');

                        $calc_total_price = $cartProduct->productDetail->price *  $cartProduct->quantity;
                    }

                } else {

                    $calc_total_price = $cartProduct->productDetail->price *  $cartProduct->quantity;
    
                    info('five calc');

                    // if($cartProduct->offer_id) {

                    //     $offer = Offer::find($cartProduct->offer_id);

                    //     if($offer != null && $offer->start_at <= $now && $offer->end_at >= $now && $offer->max_use <= OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count()) {

                    //         $calc_total_price = $offerPrice['productPriceAfterDiscount'] *  $cartProduct->quantity;

                    //     } else {

                    //         $calc_total_price = $cartProduct->productDetail->price *  $cartProduct->quantity;

                    //     }

                    // } else {
                    //     $calc_total_price = $cartProduct->productDetail->price *  $cartProduct->quantity;
                    // }
                }

                $order_product = $order->orderProducts()->create([
                    'quantity' =>  $cartProduct->quantity,
                    'product_id' => @$product_details_row->product_id,
                    'product_detail_id' => $cartProduct->product_detail_id,
                    'flash_sale_product_id' => $cartProduct->flash_sale_product_id,
                    'flash_sale_data' => $cartProduct->flash_sale_product_id  ? $cartProduct->flashSaleProduct->toJson() : null,
                    'flash_sale_price' =>  $cartProduct->flash_sale_product_id  ? $cartProduct->flashSaleProduct->price_after : null,
                    'price' => $cartProduct->productDetail->price,
                    //'total_product_price_before' => $cartProduct->productDetail->price *  $cartProduct->quantity,
                    'total_product_price_before' => $calc_total_price,

                    'offer_id' => $cartProduct->offer_id,
                    'offer_data' => $cartProduct->offer_id ? $cartProduct->offer->toJson() : null,
                    'offer_percentage' => $cartProduct->offer_id && $offerPrice != null && array_key_exists('offer_percentage',$offerPrice) ?  $offerPrice['offer_percentage'] : null,
                    'offer_price' => $cartProduct->offer_id && $offerPrice != null && array_key_exists('offer_price',$offerPrice) ? $offerPrice['offer_price'] : null,
                    'total_price' => $calc_total_price
                    // $cartProduct->flash_sale_product_id ? $cartProduct->flashSaleProduct->price_after * $cartProduct->quantity : ($cartProduct->offer_id  ? ($offerPrice['productPriceAfterDiscount'] *  $cartProduct->quantity) :    $cartProduct->productDetail->price *  $cartProduct->quantity)
                ]);
                
                $totalPrice +=  $order_product->total_price;

                $order_product->productDetail()->update([
                    'quantity' => ($order_product->productDetail->quantity - $cartProduct->quantity)
                ]);

                if ($cartProduct->flash_sale_product_id !=  null) {
                    $order->flashSaleOrders()->create([
                        'user_id' => auth('api')->id(),
                        'flash_sale_product_id' => $cartProduct->flash_sale_product_id,
                    ]);

                    $cartProduct->flashSaleProduct()->increment('sold', $cartProduct->quantity);
                }

                if ($cartProduct->offer_id !=  null) {
                    $order->offerOrders()->create([
                        'user_id' => auth('api')->id(),
                        'offer_id' => $cartProduct->offer_id,
                    ]);
                }
            }
        }

        $coupon  = $request->code != null ?    Coupon::where('code', $request->code)->first() : null;

        $shippingPriceData = $this->shippingCalculation($order->address,    $coupon);


        // test it
        $total_product_for_discount_shipping = setting('total_product_for_discount_shipping') ? setting('total_product_for_discount_shipping') : 0;

        if ($total_product_for_discount_shipping  != 0 && $order->orderProducts()->sum('total_product_price_before') >= $total_product_for_discount_shipping) {

            $shippingPriceData['shippingPrice']  = 0;
        }


        $orderPriceDetail =    $order->orderPriceDetail()->create([
            // 'coupon_id' => $request->coupon_id ? $request->coupon_id : null,
            'total_product_price_before' =>  $order->orderProducts()->sum('total_product_price_before'),
            'shipping_price' => $shippingPriceData['shippingPrice'],
            'total_price' =>   $order->orderProducts()->sum('total_price') + $shippingPriceData['shippingPrice'],
        ]);

        $order->update([
            'distance' => $shippingPriceData['distance'],
        ]);

        if ($request->code != null) {
            
            Coupon::where('code', $request->code)->increment('num_of_used', 1);

            $couponId = Coupon::where('code', $request->code)->first()->id;
            $couponData = $this->CouponPrice($couponId);

            OrderCoupon::create([
                'order_id' => $order->id,
                'user_id' => auth('api')->id(),
                'coupon_id' => $couponId,
                'coupon_data' => isset($request->code) && $couponId != null ? Coupon::find($couponId)->toJson() : null,
                'coupon_percentage' =>     $couponData['couponPercentage'],
                'coupon_price' =>    $couponData['couponPrice'],
                'order_price_detail_id' => $orderPriceDetail->id,
            ]);
            
            $orderPriceDetail->update([
                'discount_value' => $couponData['couponPrice'],
            ]);
            $orderPriceDetail = $orderPriceDetail->fresh();

            // $couponData['addtion_options'] != null ?      $orderPriceDetail->update(['shipping_price'=> 0]) :null;
        }

        $calc_sub_total = $orderPriceDetail->total_product_price_before;

        if($coupon != null) {

            $calc_sub_total = ($orderPriceDetail->discount_value < $calc_sub_total) ?  ($calc_sub_total - $orderPriceDetail->discount_value) : 0; 

        } 

        $vatData = $this->vatPrice($calc_sub_total);
        // $vatData = $this->vatPrice($orderPriceDetail->total_product_price_before - $orderPriceDetail->discount_value);

        // info($vatData);

        $orderPriceDetail->update([
            'vat_percentage' => $vatData['vat_percentage'],
            'vat_price' => $vatData['vat_price'],
            'total_price' => $orderPriceDetail->discount_value > $order->orderProducts()->sum('total_price') ?  0 +  $shippingPriceData['shippingPrice']  + $vatData['vat_price'] : ($order->orderProducts()->sum('total_price') - $orderPriceDetail->discount_value) +  $shippingPriceData['shippingPrice']  + $vatData['vat_price'],
        ]);

        ///////////////////////////////////////////////////////////////////////////////

        $file_name = generate_random_file_name() . ".png";

        $date = Carbon::now()->format('Y-m-d');
        $time = Carbon::now()->format('h:i A');

        $company = Company::first();

        if($company && $orderPriceDetail) {

            $generatedString = GenerateQrCode::fromArray([
                new Seller($company->name), // seller name
                new TaxNumber($company->tax_number), // seller tax number
                // new InvoiceDate(date('Y-m-d H:i:s.u')), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
                new InvoiceDate($date.'T'.$time.'Z'), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
                // new InvoiceDate(date('Y-m-d\TH:i:s0')), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
                new InvoiceTotalAmount($orderPriceDetail->total_price), // invoice total amount
                new InvoiceTaxAmount($orderPriceDetail->vat_price) // invoice tax amount
            ])->toBase64();

            $image_row = $company->media()->where('option',null)->first();

            if($image_row){
                QrCode::errorCorrection('H')
                    ->format('png')
                    ->encoding('UTF-8')
                    ->merge(public_path('storage/images/company/' . $image_row->media), .2 ,true)
                    ->size(500)
                    ->generate($generatedString, storage_path('app/public/images/bill/'.$file_name));
            }else{
                QrCode::errorCorrection('H')
                    ->format('png')
                    ->encoding('UTF-8')
                    ->size(500)
                    ->generate($generatedString, storage_path('app/public/images/bill/'.$file_name));
            }

            $order->update([
                'qr' => $file_name,
                'seller_name' => $company->name,
                'tax_number'  => $company->tax_number,
            ]);

        }

        ///////////////////////////////////////////////////////////////////////////////

        return   $order;
    }
}
