<?php

namespace App\Http\Resources\Api\App\Product;

use App\Http\Resources\Api\App\Help\{ColorResource, SizeResource};
use App\Models\FavouriteProduct;
use App\Models\FlashSaleOrder;
use App\Models\FlashSaleProduct;
use App\Models\Offer;
use App\Models\OfferOrder;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductDetailsResource extends JsonResource
{

    protected static $using = [];

    public static function using($using = [])
    {
        static::$using = $using;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $fav = auth()->guard('api')->user() != null ? (FavouriteProduct::where(['user_id' => auth('api')->id(), 'guest_token' => null, 'product_detail_id' => $this->id])->first()  != null ? true : false) : ($request->guest_token != null ? (FavouriteProduct::where(['guest_token' => $request->guest_token, 'product_detail_id' => $this->id, 'user_id' => null])->first()  != null ? true : false) : false);

        // $offer_id = $this->merge(static::$using)->data != null && array_key_exists('offer_id',$this->merge(static::$using)->data) ? $this->merge(static::$using)->data['offer_id'] : null;
        // $flash_sale_product_id = $this->merge(static::$using)->data != null && array_key_exists('flash_sale_product_id',$this->merge(static::$using)->data) ? $this->merge(static::$using)->data['flash_sale_product_id'] : null;

        // if($offer_id != null || $flash_sale_product_id != null) {

        //     // $have_sale = $this->have_sale;

        //     if($offer_id != null) {

        //         $offer = Offer::find($offer_id);

        //         // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->count())) {
        //         //     $have_sale = null;
        //         // } else {
        //         //     $have_sale = $this->have_sale;
        //         // }

        //         if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id)->where('created_at','>=',$offer->start_at)->where('created_at','<=',$offer->end_at); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count())) {
        //             $have_sale = null;
        //         } else {
        //            $have_sale = $this->have_sale;
        //         }

        //     } else {

        //         $flashSalesProduct = FlashSaleProduct::find($flash_sale_product_id);

        //         $order_products_count = FlashSaleOrder::whereHas('order',function($order) {
        //             $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']);
        //         })->whereHas('flash_sale_product', function ($q) use($flashSalesProduct) {
        //             $q->where('id', $flashSalesProduct->id);
        //         })->where('created_at','>=',$flashSalesProduct->flashSale->start_at)->where('created_at','<=',$flashSalesProduct->end_at)->count();

        //         // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($flashSalesProduct != null && $flashSalesProduct->quantity_for_user <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->whereHas('flashSaleOrders', function ($q) use($flashSalesProduct) {
        //         //     $q->where('flash_sale_product_id', $flashSalesProduct->id);
        //         // })->count())) {
        //         //     $have_sale = null;
        //         // } else {
        //         //     $have_sale = $this->have_sale;
        //         // }

        //         if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($flashSalesProduct != null && $flashSalesProduct->quantity_for_user <= $order_products_count)) {
        //             $have_sale = null;
        //         } else {
        //             $have_sale = $this->have_sale;
        //         }
        //     }

        // } else {
        //     $have_sale = null;
        // }

        return [
            'id' => (int)$this->id,
            'name' => (string)@$this->product->name,
            'desc' => (string)@$this->product->desc,
            'price' => (float)$this->price,
            'currency' => 'SAR',
            'color' =>  new ColorResource($this->color),
            'size' => new SizeResource($this->size),
            'image'  => $this->image,
            'code' => (string)$this->code,
            'is_fav' =>   $fav ,
            'rate_avg' => (float)$this->rate_avg,
            // 'have_sale' =>  $have_sale,
            'have_sale' =>  $this->have_sale,
            'quantity' => (float)$this->quantity,
            'product_id' => @$this->product->id,
        ];
    }
}
