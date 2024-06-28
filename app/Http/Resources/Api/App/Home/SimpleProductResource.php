<?php

namespace App\Http\Resources\Api\App\Home;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this->productDetails()->groupBy('color_id')->get());

        return [
            'id' => (int)$this->id,
            'name' => (string)$this->name,
            'desc'   => (string)$this->desc,
            'quantity'  => $this->productDetails()->sum('quantity'),
            'product_details' => SimpleProductDetailResource::collection($this->productDetails()->groupBy('color_id')->get()),
            // 'is_fav' =>false,

        ];
    }
}
