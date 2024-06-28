<?php

namespace App\Http\Resources\Api\App\Home;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleFlashSaleProductResource extends JsonResource
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
            'price_before' =>(float)$this->price_before,
            'price_after' =>(float)$this->price_after,
            'flash_sale_product' => new SimpleFlashProductResource($this->product),
        ];
    }
}
