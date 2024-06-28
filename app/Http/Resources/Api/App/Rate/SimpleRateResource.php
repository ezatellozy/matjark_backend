<?php

namespace App\Http\Resources\Api\App\Rate;

use App\Http\Resources\Api\App\Product\SimpleProductDetailsResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleRateResource extends JsonResource
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
            'rate' => (float)$this->rate,
            'comment' => (string)$this->comment,
            'product_details' => new SimpleProductDetailsResource($this->productDetail),
            // 'rate_images' => $this->images,
        ];
    }
}
