<?php

namespace App\Http\Resources\Api\Dashboard\Offer_Old;

use App\Http\Resources\Api\Dashboard\Color\ColorResource;
use App\Http\Resources\Api\Dashboard\Product\ProductDetailsResource;
use App\Http\Resources\Api\Dashboard\Product\ProductFeatureResource;
use App\Http\Resources\Api\Dashboard\Product\ProductMediaResource;
use App\Http\Resources\Api\Dashboard\Product\SimpleProductResource;
use App\Http\Resources\Api\Dashboard\Size\SizeResource;
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
