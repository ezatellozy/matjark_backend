<?php

namespace App\Http\Resources\Api\App\Cart;

use App\Http\Resources\Api\App\Product\SimpleProductDetailsResource;
use App\Models\CartOfferType;
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
        $offerStatus = '';
        if ($this->offer_id != null) {
            $offer = Offer::find($this->offer_id);
            if ($offer->type = 'buy_x_get_y') {
                // dd('khlood');
                $cartOfferBuy = CartOfferType::where(['type' => 'buy_x', 'cart_id' => $this->cart->id, 'offer_id'=> $this->offer_id])->first();
                $cartOfferGet = CartOfferType::where(['type' => 'get_y', 'cart_id' => $this->cart->id, 'offer_id'=> $this->offer_id])->first();
                if ($cartOfferBuy != null  && $offer->buyToGetOffer->buy_quantity >  $cartOfferBuy->quantity) {
                    $offerStatus = 'buy';
                } elseif (($cartOfferGet != null  && $offer->buyToGetOffer->get_quantity >  $cartOfferGet->quantity  ) || ($cartOfferGet == null &&  $cartOfferBuy != null)) {
                    $offerStatus = 'get';
                } else {
                    $offerStatus = 'finished';
                }
            }
        // dd($offer->buyToGetOffer->buy_quantity ,  $cartOfferBuy->quantity);
        }


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        SimpleProductDetailsResource::using([
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
                //         $order->where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected']); })->count()))
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

                // if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($flashSalesProduct != null && $flashSalesProduct->quantity_for_user <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->whereHas('flashSaleOrders', function ($q) use($flashSalesProduct) {
                //     $q->where('flash_sale_product_id', $flashSalesProduct->id);
                // })->count()))

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

                if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($flashSalesProduct != null && $flashSalesProduct->quantity_for_user <= $order_products_count)) {

                    $have_sale = null;
                } else {
                    $have_sale = @$this->productDetail->have_sale;
                }
            }

        } else {
            $have_sale = null;
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        return [
            'id' => (int)$this->id,
            'quantity' => (float)$this->quantity,
            'offer_id' => $this->offer_id,
            'offer_status' =>  $offerStatus,
            'product_details' => new  SimpleProductDetailResource($this->productDetail),
            'price' => $have_sale != null ? $have_sale['price_after'] : (float)($this->productDetail->price),

        ];
    }
}
