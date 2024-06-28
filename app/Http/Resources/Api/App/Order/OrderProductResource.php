<?php

namespace App\Http\Resources\Api\App\Order;

use App\Http\Resources\Api\App\Product\SimpleProductDetailsResource;
use App\Http\Resources\Api\App\ReturnOrder\ReturnOrder\ReturnOrderItemResource;
use App\Models\OrderRate;
use App\Models\ReturnOrderProduct;
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

        $rate = OrderRate::where(['user_id' => auth('api')->id(), 'product_detail_id' => $this->productDetail->id, 'order_id' => $this->order->id])->first();

        return [
            'id' => (int)$this->id,
            'quantity' => (float)$this->quantity,
            'price' => (float) round($this->total_price / $this->quantity),
            'total_price' => (float)$this->total_price,
            'offer_discount' =>(float)$this->offer_price,
            'item_after_discount' => $this->offer_price != null && ($this->price > $this->offer_price )?(float)($this->price - $this->offer_price ): 0,
            // 'name' => (string)$this->productDetail->product->name,
            //'rate' =>   $rate != null  ? (float)  $rate->rate : null,
            'rate' =>   $rate != null  ? (float)  $rate->rate : null,
            'product_details' => new  SimpleProductDetailsResource($this->productDetail),
            'details_of_return_item' =>$this->order->returnOrder != null ?  new ReturnOrderItemResource(ReturnOrderProduct::where(['order_product_id'=> $this->id ])->first()) : null,
        ];
    }
}
