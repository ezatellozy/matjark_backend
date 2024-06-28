<?php

namespace App\Http\Resources\Api\Provider\Offer_Old;

use App\Http\Resources\Api\Provider\Color\ColorResource;
use App\Http\Resources\Api\Provider\Product\ProductDetailsResource;
use App\Http\Resources\Api\Provider\Product\ProductFeatureResource;
use App\Http\Resources\Api\Provider\Product\ProductMediaResource;
use App\Http\Resources\Api\Provider\Product\SimpleProductResource;
use App\Http\Resources\Api\Provider\Size\SizeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferProductResource extends JsonResource
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
            'product'                  => ['id' => $this->id, 'name' => $this->name],
            'product_details'          => OfferProductDetailResource::collection($this->productDetails),
            'offer_product_detail_ids' => $this->product_detail_ids
        ];
    }
}
