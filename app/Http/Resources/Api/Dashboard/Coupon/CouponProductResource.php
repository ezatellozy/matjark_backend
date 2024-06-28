<?php

namespace App\Http\Resources\Api\Dashboard\Coupon;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponProductResource extends JsonResource
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
            'product'              => ['id' => $this->id, 'name' => $this->name ,'code'=> $this->code],
            'product_details_show' => CouponProductDetailResource::collection($this->productDetails),
            'product_details_id'   => $this->product_detail_ids,
        ];
    }
}
