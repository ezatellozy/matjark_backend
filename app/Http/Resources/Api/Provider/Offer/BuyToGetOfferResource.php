<?php

namespace App\Http\Resources\Api\Provider\Offer;

use App\Http\Resources\Api\Provider\Category\CategorySimpleResource;
use App\Http\Resources\Api\Provider\Product\SimpleProductDetailResource;
use App\Http\Resources\Api\Provider\Product\SimpleProductResource;
use App\Models\Category;
use App\Models\ProductDetails;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyToGetOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $buy_apply = [];
        $get_apply = [];
        if ($this->buy_apply_on == 'special_categories')
        {
            $buy_apply = Category::whereIn('id', $this->buy_apply_ids)->get();
        }
        elseif ($this->buy_apply_on == 'special_products')
        {
            $buy_apply = ProductDetails::whereIn('id', $this->buy_apply_ids)->get();
        }

        if ($this->get_apply_on == 'special_categories')
        {
            $get_apply = Category::whereIn('id', $this->get_apply_ids)->get();
        }
        elseif ($this->get_apply_on == 'special_products')
        {
            $get_apply = ProductDetails::whereIn('id', $this->get_apply_ids)->get();
        }

        return [
            "id"              => (int) $this->id,
            "buy_quantity"    => (int) $this->buy_quantity,
            "buy_apply_on"    => (string) $this->buy_apply_on,
            "buy_apply_ids"   => $this->buy_apply_ids,
            "buy_apply"       => $this->buy_apply_on == 'special_categories' ? CategorySimpleResource::collection($buy_apply) : SimpleProductDetailResource::collection($buy_apply),
            "get_quantity"    => (int) $this->get_quantity,
            "get_apply_on"    => (string) $this->get_apply_on,
            "get_apply_ids"   => $this->get_apply_ids,
            "get_apply"       => $this->buy_apply_on == 'special_categories' ? CategorySimpleResource::collection($get_apply) : SimpleProductDetailResource::collection($get_apply),
            "discount_type"   => (string) $this->discount_type,
            "discount_amount" => (string) $this->discount_amount,
        ];
    }
}
