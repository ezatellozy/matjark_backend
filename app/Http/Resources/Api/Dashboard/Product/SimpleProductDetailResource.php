<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\Dashboard\Coupon\CouponProductDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductDetailResource extends JsonResource
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
            'id'        => (int) $this->id,
            'product'   => $this->product ? new SimpleProductResource($this->product) : null,
            'quantity'  => (int) $this->quantity,
            'price'     => (double) $this->price,
            'code' => (string)$this->code,
            'have_sale' => (bool) $this->have_sale,


        ];
    }
}
