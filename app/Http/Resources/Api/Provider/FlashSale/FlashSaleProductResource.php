<?php

namespace App\Http\Resources\Api\Provider\FlashSale;

use Illuminate\Http\Resources\Json\JsonResource;

class FlashSaleProductResource extends JsonResource
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
            'id'                   => (int) $this->id,
            'quantity'             => (int) $this->quantity,
            'quantity_for_user'    => (int) $this->quantity_for_user,
            'ordering'             => (int) $this->ordering,
            'discount_type'        => (string) $this->discount_type,
            'discount_amount'      => (double) $this->discount_amount,
            'price_before'         => (double) $this->price_before,
            'price_after'          => (double) $this->price_after,
            'product'              => $this->product ? ['id' => $this->product->id, 'name' => $this->product->name] : null,
            'product_details_show' => $this->product ? FlashSaleProductDetailResource::collection($this->product->productDetails) : [],
            'product_details_id'   => $this->product_detail_id,
        ];
    }
}
