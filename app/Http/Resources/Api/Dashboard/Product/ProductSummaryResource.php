<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\Dashboard\Color\ColorItemResource;
use App\Http\Resources\Api\Dashboard\Color\ColorResource;
use App\Http\Resources\Api\Dashboard\Size\SizeItemResource;
use App\Http\Resources\Api\Dashboard\Size\SizeResource;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSummaryResource extends JsonResource
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
            'id'                 => (int) $this->id,
            'color'              => ColorItemResource::make($this->color),
            'size'               => SizeItemResource::make($this->size),
            'quantity'           => $this->quantity,
            'price'              => $this->price,
            'image'              => $this->image,

        ];
    }
}
