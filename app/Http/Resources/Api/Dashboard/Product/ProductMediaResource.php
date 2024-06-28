<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductMediaResource extends JsonResource
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
            'id'    => (int) $this->id,
            'image' => asset('storage/images/products/' . $this->product_id . '/' . $this->product_details_id . '/' . $this->media),
            'image_alt_ar' => $this->alt_ar,
            'image_alt_en' => $this->alt_en
        ];
    }
}
