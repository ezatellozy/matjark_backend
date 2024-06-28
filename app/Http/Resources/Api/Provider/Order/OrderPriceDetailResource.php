<?php

namespace App\Http\Resources\Api\Provider\Order;

use App\Models\OrderProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPriceDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
                $total_product_after_discount  = OrderProduct::where('order_id',$this->order_id)->sum('total_price') == $this->total_product_price_before ? 0 :  OrderProduct::where('order_id',$this->order_id)->sum('total_price');

        return [
            'id'                  => (int) $this->id,
            'coupon_price'        => (float) $this->coupon_price,
            'offer_price'         => (float) $this->offer_price,
            'discount'            => (float) $this->discount_value,
            'vat_percentage'      => (float) $this->vat_percentage,
            'vat_price'           => (float) $this->vat_price,
            'shipping_price'      => (float) $this->shipping_price,
            'total_product_price' => (float) $this->total_product_price_before,
                        'total_product_after_discount' => (float)  $total_product_after_discount,

            'total_price'         => (float) $this->total_price,
        ];
    }
}
