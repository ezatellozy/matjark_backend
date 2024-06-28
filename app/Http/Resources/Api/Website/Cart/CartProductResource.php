<?php

namespace App\Http\Resources\Api\Website\Cart;

use App\Models\FlashSaleOrder;
use App\Models\FlashSaleProduct;
use App\Models\Offer;
use App\Models\OfferOrder;
use App\Models\Order;
use App\Models\OrderProduct;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        SimpleProductDetailResource::using([
            'offer_id' => $this->offer_id,
            'flash_sale_product_id' => $this->flash_sale_product_id
        ]);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $offer_id = $this->offer_id;
        $flash_sale_product_id = $this->flash_sale_product_id;

        $now = Carbon::now();

        if($offer_id != null || $flash_sale_product_id != null) {

            // $have_sale = $this->have_sale;

            if($offer_id != null) {

                $offer = Offer::find($offer_id);

                // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->count())) {
                // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= OrderProduct::whereHas('order',function($order) {
                //     $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count()))
                // {
                //     $have_sale = null;
                // } else {
                //     $have_sale = @$this->productDetail->have_sale;
                // }

                // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id)->where('created_at','>=',$offer->start_at)->where('created_at','<=',$offer->end_at); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count())) {
                if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && $offer->start_at <= $now && $offer->end_at >= $now && ($offer->max_use <= OfferOrder::whereHas('offer',function($order_offer) use($offer) { $order_offer->where('offer_id',$offer->id); })->whereHas('order',function($order) { $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count())) {
                        $have_sale = null;
                } else {
                    $have_sale = @$this->productDetail->have_sale;
                }


            } else {

                // $flashSalesProduct = FlashSaleProduct::find($flash_sale_product_id);

                $flashSalesProduct = FlashSaleProduct::where('id',$flash_sale_product_id)->whereHas('flashSale',function($flashSale) use($now) {
                    $flashSale->where('start_at', '<=',  $now)->where('end_at', '>=',  $now);
                })->first();

                if($flashSalesProduct) {

                    // $order_products_count = OrderProduct::whereHas('order',function($order) use($flashSalesProduct) {
                    //     $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])
                    //     ->whereHas('flashSaleOrders', function ($q) use($flashSalesProduct)  {
                    //         $q->where('flash_sale_product_id', $flashSalesProduct->id);
                    //     });
                    // })->count();

                    $order_products_count = FlashSaleOrder::whereHas('order',function($order) {
                        $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']);
                    })->whereHas('flash_sale_product', function ($q) use($flashSalesProduct) {
                        $q->where('id', $flashSalesProduct->id);
                    })
                    // ->where('created_at','>=',$flashSalesProduct->flashSale->start_at)->where('created_at','<=',$flashSalesProduct->end_at)
                    ->count();

                } else {
                    $order_products_count = 0;
                }



                // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($flashSalesProduct != null && $flashSalesProduct->quantity_for_user <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->whereHas('flashSaleOrders', function ($q) use($flashSalesProduct) {
                //     $q->where('flash_sale_product_id', $flashSalesProduct->id);
                // })->count())) {

                if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($flashSalesProduct != null && $flashSalesProduct->quantity_for_user <= $order_products_count)) {
                    $have_sale = null;
                } else {
                    $have_sale = @$this->productDetail->have_sale;
                }

                // info('not ok');

            }

        } else {
            $have_sale = null;
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        return [
            'id' => (int)$this->id,
            'offer_id' => $this->offer_id,
            'quantity'=> (float)$this->quantity,
            'name' => $this->productDetail && $this->productDetail->product ? (string)@$this->productDetail->product->name : null,
            'slug' => $this->productDetail && $this->productDetail->product ? (string)@$this->productDetail->product->slug : null,
            'product_details' => new  SimpleProductDetailResource($this->productDetail),
            'price' => $have_sale != null ? $have_sale['price_after'] : (float)($this->productDetail->price),
            // 'price' => $have_sale != null ? 'yes' : 'no',
            'currency' => 'SAR',
        ];
    }
}
