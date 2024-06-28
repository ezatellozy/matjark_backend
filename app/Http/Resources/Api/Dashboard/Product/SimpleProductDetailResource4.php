<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\Dashboard\Coupon\CouponProductDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductDetailResource4 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $productDetails = $this->productDetails != null ? $this->productDetails()->first() : null;

        return [
            'id'        => (int) $this->id,
            'product'   => $this != null ? new SimpleProductResource($this) : null,
            'quantity'  => (int) ($productDetails != null ? $productDetails->quantity : 0),
            'price'     => (double) ($productDetails != null ? $productDetails->price : 0),
            'code' => (string) ($productDetails != null ? $productDetails->code : 0),
            'have_sale' => (bool) $productDetails != null ? $productDetails->have_sale : 0,


        ];
    }
}
