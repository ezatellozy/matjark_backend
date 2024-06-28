<?php

namespace App\Http\Resources\Api\Provider\Coupon;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponCategoryResource extends JsonResource
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
            'root_id'     => (int) $this->root->id,
            'category_id' => (int) $this->id
        ];
    }
}
