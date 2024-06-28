<?php

namespace App\Http\Resources\Api\Website\Home;

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
            'percentage'  =>  (($this->price_before - $this->price_after )/$this->price_before)* 100,
            'sold' => (float)(($this->sold / $this->quantity) / 100),

            'flash_sale_product' => new SimpleFlashProductResource($this->product),
        ];
    }
}
