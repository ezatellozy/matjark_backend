<?php

namespace App\Http\Resources\Api\Website\Coupon;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'id' => (int)$this->id,
            'code' => (string)$this->code,
            'discount_type' => (string)$this->discount_type,
            'discount_amount' => (float)$this->discount_amount,
            'start_at' => $this->start_at->format('Y-m-d'),
            'end_at' => $this->end_at->format('Y-m-d'),
            'image' => $this->image,
            'applly_coupon_on'  =>  (string)$this->applly_coupon_on,
        ];
    }
}
