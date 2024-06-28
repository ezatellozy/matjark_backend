<?php

namespace App\Http\Resources\Api\Provider\Offer;

use App\Http\Resources\Api\Provider\Category\CategorySimpleResource;
use App\Http\Resources\Api\Provider\Product\SimpleProductResource;
use App\Http\Resources\Api\Provider\Product\SimpleProductDetailResource;
use App\Models\Category;
use App\Models\ProductDetails;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountOfOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $apply = [];

        if ($this->apply_on == 'special_categories')
        {
            $apply = Category::whereIn('id', $this->apply_ids)->get();
        }
        elseif ($this->apply_on == 'special_products')
        {
            $apply = ProductDetails::whereIn('id', $this->apply_ids)->get();
        }
        return [
            "id"              => (int) $this->id,
            "discount_type"   => (string) $this->discount_type,
            "discount_amount" => (double) $this->discount_amount,
            "max_discount"    => (double) $this->max_discount,
            "apply_on"        => (string) $this->apply_on,
            "apply_ids"       => $this->apply_ids,
            "payment_type"    => $this->payment_type,
            "apply"           => $this->apply_on == 'special_categories' ? CategorySimpleResource::collection($apply) : SimpleProductDetailResource::collection($apply),
            "min_type"        => (string) $this->min_type,
            "min_value"       => (double) $this->min_value,
        ];
    }
}
