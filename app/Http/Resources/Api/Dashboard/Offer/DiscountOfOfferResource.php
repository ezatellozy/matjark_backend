<?php

namespace App\Http\Resources\Api\Dashboard\Offer;

use App\Http\Resources\Api\Dashboard\Category\CategorySimpleNewResource;
use App\Http\Resources\Api\Dashboard\Category\CategorySimpleResource;
use App\Http\Resources\Api\Dashboard\Product\SimpleProductDetail2Resource;
use App\Http\Resources\Api\Dashboard\Product\SimpleProductResource;
use App\Http\Resources\Api\Dashboard\Product\SimpleProductDetailResource;
use App\Models\Category;
use App\Models\Product;
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
            // $apply = Category::whereIn('id', $this->apply_ids)->get();

            $apply = Category::whereIn('id', $this->apply_ids)->where('position', 'second_sub')->get();

            $apply->each(function ($category) {
                data_set($category, 'root', root($category));
            });
        }
        elseif ($this->apply_on == 'special_products')
        {
            // $apply = ProductDetails::whereIn('id', $this->apply_ids)->get();

            $apply = Product::whereHas('productDetails', function($query)  {
                $query->whereIn('id', $this->apply_ids);
            })->get();

            $apply->each(function ($product)  {
                data_set($product, 'product_detail_ids', array_intersect($this->apply_ids, $product->productDetails->pluck('id')->toArray()));
            });
        }
        return [
            "id"              => (int) $this->id,
            "discount_type"   => (string) $this->discount_type,
            "discount_amount" => (double) $this->discount_amount,
            "max_discount"    => (double) $this->max_discount,
            "apply_on"        => (string) $this->apply_on,
            "apply_ids"       => $this->apply_ids,
            "payment_type"    => $this->payment_type,
            // "apply"           => $this->apply_on == 'special_categories' ? CategorySimpleResource::collection($apply) : SimpleProductDetailResource::collection($apply),
            "apply"           => $this->apply_on == 'special_categories' ? CategorySimpleNewResource::collection($apply) : SimpleProductDetail2Resource::collection($apply),
            "min_type"        => (string) $this->min_type,
            "min_value"       => (double) $this->min_value,
        ];
    }
}
