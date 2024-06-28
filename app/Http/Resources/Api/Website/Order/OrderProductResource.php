<?php

namespace App\Http\Resources\Api\Website\Order;

use App\Http\Resources\Api\Website\Product\SimpleProductDetailsResource;
use App\Models\OrderRate;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $rate = OrderRate::where(['user_id' => auth('api')->id(), 'product_detail_id' => $this->productDetail != null ? $this->productDetail->id : 0, 'order_id' => $this->order != null ? $this->order->id : 0])->first();

        return [
            'id' => (int)$this->id,
            'quantity' => (float)$this->quantity,
            'price' => (float) round($this->total_price / $this->quantity),
            'total_price' => (float)$this->total_price,
            'offer_discount' =>(float)$this->offer_price,
            'item_after_discount' => $this->offer_price != null && ($this->price > $this->offer_price) ?(float)($this->price - $this->offer_price ): 0,
            // 'name' => (string)$this->productDetail->product->name,
            'rate' =>   $rate != null  ? (float)  $rate->rate : null,
            'product_details' => new  SimpleProductDetailsResource($this->productDetail),
        ];
    }
}
