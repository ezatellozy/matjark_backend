<?php

namespace App\Http\Resources\Api\Provider\FlashSale;

use App\Http\Resources\Api\Provider\Color\ColorResource;
use App\Http\Resources\Api\Provider\Product\ProductFeatureResource;
use App\Http\Resources\Api\Provider\Product\ProductMediaResource;
use App\Http\Resources\Api\Provider\Size\SizeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashSaleProductDetailResource extends JsonResource
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
            'id'       => $this->id,
            'name'     => $this->product->name,
            'color'    => $this->color ? new ColorResource($this->color) : null,
            'size'     => $this->size ? new SizeResource($this->size) : null,
            'features' => $this->features ? ProductFeatureResource::collection($this->features) : null,
            'quantity' => (int) $this->quantity,
            'price'    => (double) $this->price,
            'rate_avg' => (double) $this->rate_avg,
            'media'    => $this->media ? ProductMediaResource::collection($this->media) : null,
        ];
    }
}
