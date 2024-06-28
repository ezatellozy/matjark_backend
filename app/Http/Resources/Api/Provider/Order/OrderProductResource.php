<?php

namespace App\Http\Resources\Api\Provider\Order;

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
        return [
            'id'          => (int) $this->id,
            'quantity'    => (float) $this->quantity,
            'price'       => (float) $this->price,
            'total_price' => (float) $this->total_price,
            'total_price' => (float) $this->total_price,
            'name'        => (string) $this->productDetail->product->name,
            'code'     => $this->productDetail->product->code != null ? (string) $this->productDetail->product->code : null,
            'product_id'  => (int)$this->productDetail->product->id ,
            'product_details' => $this->productDetail ? new OrderProductDetailsResource($this->productDetail) : null,
        ];
    }
}
