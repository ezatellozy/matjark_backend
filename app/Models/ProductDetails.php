<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ProductDetails extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'features' => 'json',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function trans()
    {
        return $this->hasOne(ProductTranslation::class, 'product_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function orderRate()
    {
        return $this->hasMany(OrderRate::class, 'product_detail_id');
    }


    public function getImageAttribute()
    {
        $image = $this->media()->exists() ? 'storage/images/products/' . $this->media()->first()->product_id . '/' . $this->media()->first()->product_details_id . '/' . $this->media()->first()->media : 'dashboardAssets/images/backgrounds/placeholder_image.png';
        return asset($image);
    }

    public function getCropImageAttribute()
    {
        $image = $this->media()->exists() ? 'storage/images/products/' . $this->media()->first()->product_id . '/' . $this->media()->first()->product_details_id . '/crop/' . $this->media()->first()->media : 'dashboardAssets/images/backgrounds/placeholder_image.png';
        return asset($image);
    }

    public function getTwoRandImagesAttribute()
    {
        // $images = $this->media()->inRandomOrder()->take(2)->get();

        $product_id = $this->product_id;
        $color_id = $this->color_id;


        $product_details = ProductDetails::where('product_id', $product_id)->where('color_id', $color_id)->orderBy('id', 'asc')->first();

        if($product_details) {
            $images = $product_details->media()->take(2)->get();
        } else {
            $images = [];
        }

        $arr = [];
        $i = 0;
        $locale =  app()->getLocale()?? 'ar';
        $alt = "alt_$locale";
        foreach ($images as $value) {
            $i++;
            $image = $value->media  != null ? 'storage/images/products/' . $value->product_id . '/' . $value->product_details_id . '/' . $value->media : null;
            $arr[$i]['id'] =  $value->id;
            $arr[$i]['image'] =  $value->media != null ?  $value->media : null;
            $arr[$i]['url'] =  $image != null ? asset($image) : null;

            $arr[$i]['image_alt'] =  $value->$alt != null ?  $value->$alt : null;

            // $arr[$i]['image_alt_ar'] =  $value->alt_ar != null ?  $value->alt_ar : null;
            // $arr[$i]['image_alt_en'] =  $value->alt_en != null ?  $value->alt_en : null;

        }
        return $arr;
    }

    public function getTwoRandCropImagesAttribute()
    {
        $images = $this->media()->inRandomOrder()->take(2)->get();
        $arr = [];
        $i = 0;
        foreach ($images as $value) {
            $i++;
            $image = $value->media  != null ? 'storage/images/products/' . $value->product_id . '/' . $value->product_details_id . '/crop/' . $value->media : null;
            $arr[$i]['id'] =  $value->id;
            $arr[$i]['image'] =  $value->media != null ?  $value->media : null;
            $arr[$i]['url'] =  $image != null ? asset($image) : null;
        }
        return $arr;
    }

    public function getImagesAttribute()
    {
        $images = $this->media()->get();
        $arr = [];
        $i = 0;
        $locale =  app()->getLocale()?? 'ar';
        $alt = "alt_$locale";
        foreach ($images as $value) {
            $i++;
            $image = $value->media  != null ? 'storage/images/products/'  . $value->product_id . '/' . $value->product_details_id . '/' . $value->media : null;
            $arr[$i]['id'] =  $value->id;
            $arr[$i]['image'] =  $value->media != null ?  $value->media : null;
            $arr[$i]['url'] =  $image != null ? asset($image) : null;

            $arr[$i]['image_alt'] =  $value->$alt != null ?  $value->$alt : null;

            // $arr[$i]['image_alt_ar'] =  $value->alt_ar != null ?  $value->alt_ar : null;
            // $arr[$i]['image_alt_en'] =  $value->alt_en != null ?  $value->alt_en : null;
        }
        return $arr;
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function flashSalesProduct()
    {
        return $this->hasMany(FlashSaleProduct::class, 'product_detail_id');
    }

    public function offerProducts()
    {
        return $this->hasMany(OfferProductDetail::class, 'product_detail_id');
    }

    public function offerProductDetails()
    {
        return $this->belongsToMany(Offer::class, 'offer_product_detail', 'product_detail_id', 'offer_id');
    }
    // public function  getHaveFlashSaleAttribute()
    // {
    //     $now = Carbon::now();

    //     $flashSale = $this->flashSalesProduct()->where(\DB::raw('quantity - sold'), '>=', 0)->whereHas('flashSale', function ($q) use ($now) {

    //         $q->whereDate('start_at', '<=',  $now);
    //         $q->whereDate('end_at', '>=',  $now);
    //         $q->where('is_active', true);
    //     })->first();
    //     $percentage = 0;
    //     if ($flashSale != null) {
    //         $percentage =       (($this->price - $flashSale->price_after) / $this->price) * 100;
    //     }

    //     $data  = null;
    //     if ($flashSale  != null) {
    //         $data  = [
    //             'key' =>    'flash_sale',
    //             'percentage' =>  (float)$percentage,
    //             'price_after' => (float)$flashSale->price_after,
    //             'sold'    =>  (float)(($flashSale->sold / $flashSale->quantity) / 100),
    //             'is_reminder'  => auth('api')->id()  != null &&  $flashSale != null ? (Reminder::where(['flash_sale_product_id' =>   $flashSale->id, 'user_id' => auth('api')->id(), 'status' => 'wait'])->first() != null  ? true : false) : false,
    //             'end_at' =>  $flashSale->flashSale->end_at,
    //         ];
    //     }
    //     return $data;
    // }


    public function getHaveSaleAttribute()
    {
        //offer , flash_sale
        // dd(request()->type);
        $now  = Carbon::now();

        $data = null;

        // new code of offer
        $route = \Request::route()->getPrefix();


        $flashSale = $this->flashSalesProduct()->where(\DB::raw('quantity - sold'), '>=', 0)->whereHas('flashSale', function ($q) use ($now) {
            if (request()->type == 'later') {
                $q->whereDate('start_at', '>',   now());
                $q->where('is_active', true);
            } else {
                $q->whereDate('start_at', '<=',  $now);
                $q->whereDate('end_at', '>=',  $now);
                $q->where('is_active', true);
            }
        })->first();


        $offers = Offer::where('is_active', true)
        ->where('remain_use', '>', 0)
        ->where('start_at', '<=',  $now)
        ->where('end_at', '>=',  $now)
        ->whereIn('display_platform',  [$route,'both'])
        ->get();

        $new_offer = null;

        foreach($offers as $item) {

            if($item->discountOfOffer != null) {

                if($item->discountOfOffer->apply_on == 'special_products') {
                    if(is_array($item->discountOfOffer->apply_ids) && in_array($this->id,$item->discountOfOffer->apply_ids)) {
                        $new_offer = $item;
                    }
                } else {
                    $category_product_details = $this->product()->whereHas('categoryProducts', function ($q) use($item) {
                        $q->whereIn('category_id', $item->discountOfOffer->apply_ids);
                    })->first();

                    if($category_product_details != null) {
                        $new_offer = $item;
                    }
                }

            } elseif($item->buyToGetOffer != null) {

                if(is_array($item->buyToGetOffer->buy_apply_ids) && in_array($this->id,$item->buyToGetOffer->buy_apply_ids)) {
                    $new_offer = $item;
                }

                if($item->buyToGetOffer->get_apply_on == 'special_products') {
                    if(is_array($item->buyToGetOffer->buy_apply_ids) && in_array($this->id,$item->buyToGetOffer->buy_apply_ids)) {
                        $new_offer = $item;
                    }
                } else {
                    $category2_product_details = $this->product()->whereHas('categoryProducts', function ($q) use($item) {
                        $q->whereIn('category_id', $item->buyToGetOffer->buy_apply_ids);
                    })->first();

                    if($category2_product_details != null) {
                        $new_offer = $item;
                    }
                }
            }
        }

        // $offer = Offer::where('is_active', true)
        //     ->whereHas('discountOfOffer',function($discountOfOffer) {
        //         $discountOfOffer->whereJsonContains('apply_ids', $this->id);
        //     })
        //     ->whereHas('buyToGetOffer',function($buyToGetOffer) {
        //         $buyToGetOffer->whereJsonContains('buy_apply_ids', $this->id);
        //     })
        //     ->where('remain_use', '>', 0)
        //     ->whereDate('start_at', '<=',  $now)
        //     ->whereDate('end_at', '>=',  $now)
        //     ->whereIn('display_platform',  [$route,'both'])
        //     ->latest()
        //     ->first();


        // dd(11,$offer,$this->id);

        $offer = $new_offer;

        $buyOffer = false;
        $getOffer = false;
        $dataOfOffer = null;
        $cartForBuy = null;
        $buyToGetOffer = null;

        if ($offer != null) {

            // dd($offer->id,$offer->type);

            if ($offer->type ==  'buy_x_get_y') {
                $buyToGetOffer = BuyToGetOffer::where('offer_id', $offer->id)->first();
                if ($buyToGetOffer->buy_apply_on  == 'special_categories') {
                    $buyOfferData =            $this->product()->whereHas('categoryProducts', function ($q) use ($buyToGetOffer) {
                        $q->whereIn('category_id', $buyToGetOffer->buy_apply_ids);
                    })->first();
                    $buyOffer =     $buyOfferData  == null ? false : true;
                } elseif ($buyToGetOffer->buy_apply_on  == 'special_products') {

                    $buyOffer =  in_array($this->id,    $buyToGetOffer->buy_apply_ids);
                }
                if ($buyToGetOffer->get_apply_on  == 'special_categories') {
                    $getOfferData =            $this->product()->whereHas('categoryProducts', function ($q) use ($buyToGetOffer) {
                        $q->whereIn('category_id', $buyToGetOffer->get_apply_ids);
                    })->first();
                    $getOffer =     $getOfferData  == null ? false : true;
                } elseif ($buyToGetOffer->get_apply_on  == 'special_products') {
                    $getOffer =   in_array($this->id,    $buyToGetOffer->get_apply_ids);
                }

                if (auth()->guard('api')->user() != null && auth()->guard('api')->user()->cart) {
                    $cartForBuy = CartOfferType::where(['offer_id' => $offer->id, 'cart_id' =>  auth()->guard('api')->user()->cart->id, 'type' => 'buy_x'])->where('quantity', $buyToGetOffer->buy_quantity)->first();
                }
            } else {

                $dataOfOffer =   $offer->discountOfOffer;

                // dd($dataOfOffer,$offer,$dataOfOffer->apply_on);

                if ($dataOfOffer && $dataOfOffer->apply_on  == 'special_categories') {
                    $offerData =            $this->product()->whereHas('categoryProducts', function ($q) use ($dataOfOffer) {
                        $q->whereIn('category_id', $dataOfOffer->apply_ids);
                    })->first();
                    // dd($dataOfOffer->apply_ids);
                    $getOffer =     $offerData  == null ? false : true;
                } else {
                    // dd($this->id,    @$dataOfOffer->apply_ids);
                    $getOffer =   $dataOfOffer != null && is_array(@$dataOfOffer->apply_ids) ? in_array($this->id,    @$dataOfOffer->apply_ids) : [];
                }
            }

            // dd($buyToGetOffer != null  , $buyOffer == true , $getOffer == false);

            // dd($buyToGetOffer != null  && $buyOffer == true && $getOffer == false);
            if ($buyToGetOffer != null  && $buyOffer == true && $getOffer == true) {
                $data  = [
                    'key' =>    'offer',
                    'key_id' =>    (int)$offer->id,
                    'typeOfOffer'   => $offer->type,
                    'typeOfProduct'  => 'buyAndGet',
                    'discount_type' => $buyToGetOffer->discount_type,
                    'percentage' =>  $buyToGetOffer->discount_type == 'percentage' ? (float)$buyToGetOffer->discount_amount : null,
                    'price_after' =>  $buyToGetOffer->discount_type == 'percentage' ? (float)($this->price - (($buyToGetOffer->discount_amount * $this->price) / 100)) : 0.0,
                    'sold'    => null,
                    'is_reminder'  =>  false,
                    'end_at' =>  $offer->end_at,
                    'end_at_for_web' => $offer->end_at->format('Y-m-d H:i:s'),
                    'is_valid' =>   $cartForBuy != null  ? true : false,

                ];
            } elseif ($buyToGetOffer != null  && $buyOffer == true && $getOffer == false) {
                $data  = [
                    'key' =>    'offer',
                    'key_id' =>    (int)$offer->id,
                    'typeOfOffer'   => $offer->type,
                    'typeOfProduct'  => 'buy',
                    'discount_type' => $buyToGetOffer->discount_type,
                    'percentage' =>  $buyToGetOffer->discount_type == 'percentage' ? (float)$buyToGetOffer->discount_amount : null,
                    'price_after' => null,
                    'sold'    => null,
                    'is_reminder'  =>  false,
                    'end_at' =>  $offer->end_at,
                    'end_at_for_web' => $offer->end_at->format('Y-m-d H:i:s'),

                    'is_valid' =>  null,


                ];
            } elseif ($buyToGetOffer != null  && $buyOffer == false && $getOffer == true) {
                $data  = [
                    'key' =>    'offer',
                    'key_id' =>    (int)$offer->id,
                    'typeOfOffer'   => $offer->type,
                    'typeOfProduct'  => 'get',
                    'discount_type' => $buyToGetOffer->discount_type,
                    'percentage' =>  $buyToGetOffer->discount_type == 'percentage' ? (float)$buyToGetOffer->discount_amount : null,
                    'price_after' =>  $buyToGetOffer->discount_type == 'percentage' ? (float)($this->price - (($buyToGetOffer->discount_amount * $this->price) / 100)) : 0.0,
                    'sold'    => null,
                    'is_reminder'  =>  false,
                    'end_at' =>  $offer->end_at,
                    'end_at_for_web' => $offer->end_at->format('Y-m-d H:i:s'),

                    'is_valid' =>   $cartForBuy != null  ? true : false,

                ];
            } elseif ($dataOfOffer != null &&    $getOffer == true) {

                $priceAfter = 0;
                if($dataOfOffer->discount_type == 'percentage' ){
                    $discountValue = ($dataOfOffer->discount_amount * $this->price) / 100;
                    $this->price  >     $discountValue ?$priceAfter =    ( $this->price -     $discountValue ) :$priceAfter = 0;
                }else{
                                        $this->price  >    $dataOfOffer->discount_amount ?$priceAfter =    ( $this->price -   $dataOfOffer->discount_amount ) :$priceAfter = 0;

                }
                $data  = [
                    'key' =>    'offer',
                    'key_id' =>    (int)$offer->id,
                    'typeOfOffer'   => $offer->type,
                    'typeOfProduct'  => 'fixAmountOrPercentage',
                    'discount_type' => $dataOfOffer->discount_type,
                    'percentage' =>  $dataOfOffer->discount_type == 'percentage' ? (float)$dataOfOffer->discount_amount : (100 * $dataOfOffer->discount_amount) / $this->price,
                  'price_after' =>      $priceAfter,

                    // 'price_after' =>  $dataOfOffer->discount_type == 'percentage' ? (float)($this->price - (($dataOfOffer->discount_amount * $this->price) / 100)) :$this->price - $dataOfOffer->discount_amount,
                    'sold'    => null,
                    'is_reminder'  =>  false,
                    'end_at' =>  $offer->end_at,
                    'end_at_for_web' => $offer->end_at->format('Y-m-d H:i:s'),

                    'is_valid' =>   null,

                ];
            } else {
                $data = null;
            }


            // OrderProduct::whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count();

            // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->count())) {

            // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= OrderProduct::whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count())) {



            // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id)->where('created_at','>=',$offer->start_at)->where('created_at','<=',$offer->end_at); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count())) {

            $countA = OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count();
            $countB = OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id); })->whereHas('order',function($order) { $order->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count();

            // info('offer');
            // info('product details is '.$this->id);

            // info($offer->max_use);
            // info($countA);

            // info($offer->num_of_use);
            // info($countB);

            if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= $countA || $offer->num_of_use <= $countB)) {
                $data['can_take_offer'] = 'no';
            // } elseif (! auth()->guard('api')->check() && $offer != null && $offer->num_of_use <= $countB) {
            //     $data['can_take_offer'] = 'no';
            } else {
                $data['can_take_offer'] = 'yes';
            }
        }

        // $data['can_take_offer2'] = $offer->max_use .'-'. Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->count();



        // $this->offerProducts()->whereHas('offer', function ($q) use ($now) {
        //     $q->whereDate('start_at', '<=',  $now);
        //     $q->whereDate('end_at', '>=',  $now);
        //     $q->where('is_active', 1);
        //     $q->where('remain_use', '>', 0);
        // })->first();

        $price_after = 0;

        if ($offer != null) {
            $price_after =       ($offer->discount_amount *  $this->price) / 100;
        }

        $percentage = 0;
        if ($flashSale != null) {
            $percentage =       (($this->price - $flashSale->price_after) / $this->price) * 100;
        }

        $flashSalesProduct = null;

        if ($flashSale  != null) {

            $data  = [
                'key' =>    'flash_sale',
                'key_id' =>     (int)$flashSale->id,
                'typeOfOffer'   => null,
                'typeOfProduct'  => null,
                'discount_type' => null,
                'percentage' =>  (float)$percentage,
                'price_after' => (float)$flashSale->price_after,
                'sold'    =>  (float)(($flashSale->sold / $flashSale->quantity) / 100),
                'is_reminder'  => auth('api')->id()  != null &&  $flashSale != null ? (Reminder::where(['flash_sale_product_id' =>   $flashSale->id, 'user_id' => auth('api')->id(), 'status' => 'wait'])->first() != null  ? true : false) : false,
                'end_at' =>  $flashSale->flashSale->end_at,
                'end_at_for_web' =>  $flashSale->flashSale->end_at->format('Y-m-d H:i:s'),

                'is_valid' =>   null,
                'is_valid' =>   null,


            ];

            // OrderProduct::whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count()

            // Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->whereHas('flashSaleOrders', function ($q)  {
            //     $q->where('flash_sale_product_id', $this->flashSalesProduct()->first()->id);
            // })->count()

            $flashSalesProduct = $this->flashSalesProduct()->first();

            if(auth()->guard('api')->check() && auth()->guard('api')->user() != null && $flashSalesProduct && $flashSalesProduct->flashSale) {

                // $order_products_count = OrderProduct::whereHas('order',function($order) {
                $order_products_count = FlashSaleOrder::whereHas('order',function($order) {
                    $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']);
                })->whereHas('flash_sale_product', function ($q) use($flashSalesProduct) {
                    $q->where('id', $flashSalesProduct->id);
                })
                // ->where('created_at','>=',$flashSalesProduct->flashSale->start_at)->where('created_at','<=',$flashSalesProduct->flashSale->end_at)
                ->count();

                $order_products_count2 = FlashSaleOrder::whereHas('order',function($order) {
                    $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']);
                })->whereHas('flash_sale_product', function ($q) use($flashSalesProduct) {
                    $q->where('id', $flashSalesProduct->id);
                })
                // ->where('created_at','>=',$flashSalesProduct->flashSale->start_at)->where('created_at','<=',$flashSalesProduct->flashSale->end_at)
                ->count();


            } else {

                $order_products_count = 0;

                $order_products_count2 = FlashSaleOrder::whereHas('order',function($order) {
                    $order->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']);
                })->whereHas('flash_sale_product', function ($q) use($flashSalesProduct) {
                    $q->where('id', $flashSalesProduct->id);
                })
                // ->where('created_at','>=',$flashSalesProduct->flashSale->start_at)->where('created_at','<=',$flashSalesProduct->flashSale->end_at)
                ->count();
            }



            // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($this->flashSalesProduct()->first() != null && ($this->flashSalesProduct()->first())->quantity_for_user <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->whereHas('flashSaleOrders', function ($q)  {

            if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($flashSalesProduct != null && $flashSalesProduct->quantity_for_user <= $order_products_count && $flashSalesProduct->quantity <= $order_products_count2)) {
                $data['can_take_offer'] = 'no';
            // } elseif (! auth()->guard('api')->check() && ($flashSalesProduct != null && $flashSalesProduct->quantity <= $order_products_count2)) {
            //     $data['can_take_offer'] = 'no';
            } else {
                $data['can_take_offer'] = 'yes';
            }
        }


        // if ($flashSale  != null || $offer  != null) {
        //     $data  = [
        //         'key' =>     $flashSale  != null ? 'flash_sale'  : ($offer   != null ? 'offer' : null),
        //         'key_id' =>     $flashSale  != null ? (int)$flashSale->id : ($offer   != null ? (int)$offer->offer->id : null),
        //         'percentage' =>   $flashSale  != null ? (float)$percentage : ($offer != null ? $offer->discount_amount : null),
        //         'price_after' => $flashSale  != null ? (float)$flashSale->price_after :  (float)$price_after,
        //         'sold'    => $flashSale != null ? (float)(($flashSale->sold / $flashSale->quantity) / 100) : null,
        //         'is_reminder'  => auth('api')->id()  != null &&  $flashSale != null ? (Reminder::where(['flash_sale_product_id' =>   $flashSale->id, 'user_id' => auth('api')->id(), 'status' => 'wait'])->first() != null  ? true : false) : false,
        //         'end_at' => $flashSale != null ? $flashSale->flashSale->end_at : null,

        //     ];
        // }

        if ($offer != null && auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($offer->max_use <= $countA || $offer->num_of_use <= $countB)) {
            return null;
        } elseif ($offer != null && ! auth()->guard('api')->check() && $offer->num_of_use <= $countB) {
            return null;
        } if ($flashSalesProduct != null && auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($flashSalesProduct->quantity_for_user <= $order_products_count || $flashSalesProduct->quantity <= $order_products_count2)) {
            return null;
        } elseif ($flashSalesProduct != null && ! auth()->guard('api')->check() && ($flashSalesProduct->quantity <= $order_products_count2)) {
            return null;
        } else {
            return $data;
        }

        // return $data;
    }

    public function getFlashSaleQuantity($exception_id = null)
    {
        $now  = Carbon::now();

        $flash_sale_quantity = $this->flashSalesProduct()->where('quantity', '!=', 'sold')->whereHas('flashSale', function ($query) use ($now, $exception_id) {
            $query->when($exception_id, function ($query) use ($exception_id) {
                $query->where('id', '!=', $exception_id);
            })->whereDate('end_at', '>=',  $now);
            $query->where('is_active', true);
        })->sum(DB::raw('quantity - sold'));

        return $flash_sale_quantity;
    }

    // public function inActiveOffer()
    // {
    //     $now  = Carbon::now();

    //     $check = $this->offerProducts()->whereHas('offer', function ($query) use ($now) {
    //         $query->whereDate('end_at', '>=',  $now);
    //         $query->where('is_active', true);
    //     })->first();

    //     return $check ? true : false;
    // }


    public function inActiveOffer()
    {
        $now  = Carbon::now();
        $getOffer = false;
        $check = Offer::where('is_active', true)->whereDate('end_at', '>=',  $now)->first();
        // dd($check);
        if ($check != null) {
            if ($check->type == 'buy_x_get_y') {
                if ($check->buyToGetOffer->buy_apply_on  == 'special_categories') {
                    $offerData =            $this->product()->whereHas('categoryProducts', function ($q) use ($check) {
                        $q->whereIn('category_id', $check->buyToGetOffer->buy_apply_ids);
                    })->first();
                    $getOffer =     $offerData  == null ? false : true;
                } else {
                    $getOffer =   in_array($this->id,    $check->buyToGetOffer->buy_apply_ids);
                }


                if ($check->buyToGetOffer->get_apply_on  == 'special_categories') {
                    $offerData =            $this->product()->whereHas('categoryProducts', function ($q) use ($check) {
                        $q->whereIn('category_id', $check->buyToGetOffer->get_apply_ids);
                    })->first();
                    $getOffer =     $offerData  == null ? false : true;
                } else {
                    $getOffer =   in_array($this->id,    $check->buyToGetOffer->get_apply_ids);
                }
            } else {
                if ($check->discountOfOffer->apply_on  == 'special_categories') {
                    $offerData =            $this->product()->whereHas('categoryProducts', function ($q) use ($check) {
                        $q->whereIn('category_id', $check->discountOfOffer->apply_ids);
                    })->first();
                    $getOffer =     $offerData  == null ? false : true;
                } else {
                    $getOffer =   in_array($this->id,    $check->discountOfOffer->apply_ids);
                }
            }
        }

        return $getOffer;
    }
    public function favProductDetails()
    {
        return $this->hasMany(FavouriteProduct::class, 'product_detail_id');
    }

    public function inActiveFlashSale()
    {
        $now  = Carbon::now();

        $check = $this->flashSalesProduct()->where('quantity', '>', 'sold')
            ->whereHas('flashSale', function ($query) use ($now) {
                $query->whereDate('end_at', '>=',  $now);
                $query->where('is_active', true);
            })->first();

        return $check ? true : false;
    }

    public function scopeScheduleFlashSale($query)
    {
        $query->whereHas('flashSalesProduct', function ($query) {
            $query->whereHas('flashSale', function ($query) {
                $query->where('end_at', '>', now());
            });
        });
    }
}
