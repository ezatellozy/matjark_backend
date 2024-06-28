<?php

namespace App\Http\Resources\Api\App\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageProductResource extends JsonResource
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
            'images' => $this->productDetail->image,
        ];
    }
}
