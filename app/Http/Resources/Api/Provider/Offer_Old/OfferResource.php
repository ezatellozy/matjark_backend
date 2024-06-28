<?php

namespace App\Http\Resources\Api\Provider\Offer_Old;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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

        $product_detail_ids = $this->offerProductDetails->pluck('id')->toArray();

        $products = Product::whereHas('productDetails', function ($query) use ($product_detail_ids) {
            $query->whereIn('id', $product_detail_ids);
        })->get();

        $products->each(function ($product) use ($product_detail_ids) {
            data_set($product, 'product_detail_ids', array_intersect($product_detail_ids, $product->productDetails->pluck('id')->toArray()));
        });

        return [
            'id'              => $this->id,
            'start_at'        => $this->start_at ? $this->start_at->format('Y-m-d') : null,
            'end_at'          => $this->end_at ? $this->end_at->format('Y-m-d') : null,
            'is_active'       => (bool) $this->is_active,
            'discount_type'   => (string) $this->discount_type,
            'discount_amount' => (double) $this->discount_amount,
            'max_use'         => (int) $this->max_use,
            'num_of_use'      => (int) $this->num_of_use,
            'remain_use'      => (int) $this->remain_use,
            'ordering'        => (int) $this->ordering,
            'image'           => (string) $this->image,
            'products'        => OfferProductResource::collection($products),
            'created_at'      => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ] + $locales;
    }
}
