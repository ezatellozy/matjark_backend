<?php

namespace App\Http\Resources\Api\Website\Order;

use App\Models\OrderPriceDetail;
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
        //$total_product_after_discount  = OrderProduct::where('order_id',$this->order_id)->sum('total_price') == $this->total_product_price_before ? 0 :  OrderProduct::where('order_id',$this->order_id)->sum('total_price');
        
        $total_product_price_before = OrderPriceDetail::where('order_id',$this->order_id)->sum('total_product_price_before');
        $discount_value = OrderPriceDetail::where('order_id',$this->order_id)->sum('discount_value');
        
        $total_product_after_discount  = ($discount_value <  $total_product_price_before) ? ($total_product_price_before - $discount_value) : 0;

        return [
            'id' => (int)$this->id,
            // 'coupon_price' => (float) optional($this->orderCoupon)->coupon_price,
            // 'discount' => (float) $this->discount_value,
            'coupon_price' => (float) $this->discount_value,
            'vat_percentage' => (float) $this->vat_percentage,
            'vat_price' => (float) $this->vat_price,
            'shipping_price' => (float) $this->shipping_price,
            'total_product' => (float) $this->total_product_price_before,
            'total_product_after_discount' => (float)    $total_product_after_discount,
            'total_price' => (float) $this->total_price,
            'currency' => 'SAR',
            
        ];
    }
}
