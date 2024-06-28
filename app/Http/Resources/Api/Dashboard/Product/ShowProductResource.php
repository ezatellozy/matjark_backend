<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\Dashboard\Color\ColorResource;
use App\Http\Resources\Api\Dashboard\Size\SizeResource;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $colors  = Color::whereIn('id', $this->productDetails()->groupBy('color_id')->pluck('color_id'))->get();
        $colorId = $request->color_id != null ? $request->color_id : ($colors->first() ? $colors->first()->id : null);
        $product_details = $this->productDetails()->when($colorId != null, function ($query) use ($colorId) {
            $query->where('color_id', $colorId);
        })->get();
        $sizes = Size::whereIn('id', $product_details->pluck('size_id')->toArray())->get();

        return [
            'id'                 => (int) $this->id,
            'code'               => $this->code,
            'name'               => (string) $this->name,
            'slug'               => (string) $this->slug,
            'desc'               => (string) $this->desc,
            'meta_canonical_tag' => (string) $this->meta_canonical_tag,
            'sizes'              => SizeResource::collection($sizes),
            'colors'             => ColorResource::collection($colors),
            // 'product_details' => ProductDetailsResource::collection($product_details),
            'product_details'    => ProductDetailsResource::collection($this->productDetails()->groupBy('color_id')->get()),

        ];
    }
}
