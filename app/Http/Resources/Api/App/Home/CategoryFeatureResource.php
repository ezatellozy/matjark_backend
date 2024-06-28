<?php

namespace App\Http\Resources\Api\App\Home;

use App\Http\Resources\Api\App\General\ColorResource;
use App\Http\Resources\Api\App\General\FeatureResource;
use App\Http\Resources\Api\App\General\SizeResource;
use App\Models\Color;
use App\Models\ProductDetails;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryFeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $colors = Color::get();
        $categories = lastLevel($this->resource);

        $max_product_price = ProductDetails::whereHas('product', function ($query) use ($categories) {
            $query->whereHas('categories', function ($query) use ($categories) {
                    if (count($categories) >0) {
                    $query->whereIn('category_id', $categories->pluck('id')->toArray());
                } else {
                    $query->where('category_id', $this->id);
                }
            });
        })->max('price');

        return [
            'colors'    => ColorResource::collection($colors),
            'sizes'     => SizeResource::collection(getCategorySizes($this->resource)),
            'features'  => FeatureResource::collection(getCategoryFeatures($this->resource)),
            'max_price' => (double) $max_product_price,
        ];
    }
}
