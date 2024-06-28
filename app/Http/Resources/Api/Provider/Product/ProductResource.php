<?php

namespace App\Http\Resources\Api\Provider\Product;

use App\Http\Resources\Api\Provider\Category\CategorySimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $locales = [];

        foreach (config('translatable.locales') as $locale) {
            $locales[$locale]['name'] = $this->translate($locale)->name;
            $locales[$locale]['desc'] = $this->translate($locale)->desc;
        }

        return [
            'id'              => (int) $this->id,
            'name'            => (string) $this->name,
            'desc'            => (string) $this->desc,
            'code'            => $this->code,
            'size_guide' => $this->size_guide,
            'rate_avg'        => (double) $this->rate_avg,
            'is_active'       => (bool) $this->is_active,
            'ordering'        => (int) $this->ordering,
            'categories'      => CategorySimpleResource::collection($this->categories),
            'main_category'   => count($this->categories) > 0 ? new CategorySimpleResource(root($this->categories->first())) : null,
            'product_details' => ProductDetailsResource::collection($this->productDetails),
            'created_at'      => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ] + $locales;
    }
}
