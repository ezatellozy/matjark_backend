<?php

namespace App\Http\Resources\Api\Dashboard\Slider;

use App\Http\Resources\Api\Dashboard\Category\CategorySimpleResource;
use App\Http\Resources\Api\Dashboard\Product\ProductItemResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            $locales[$locale]['name']      = $this->translate($locale)->name;
            $locales[$locale]['desc']      = $this->translate($locale)->desc;
            // $locales[$locale]['slug']      = $this->translate($locale)->slug;
            // $locales[$locale]['link_name'] = $this->translate($locale)->link_name;
        }


         if($this->item_type != 'products') {
            $item_id = $this->item_id;
         } else {
            $item_id = json_decode($this->item_id);
            $item_id = ProductItemResource::collection(Product::whereIn('id',$item_id)->get());
         }

        return [
            'id'          => (int) $this->id,
            'ordering'    => (int) $this->ordering,
            'is_active'   => (bool) $this->is_active,
            'images'      => $this->images,
            'image'       => $this->image,
            'type'    => (string)$this->type,
            // 'link'        => (string) $this->link,
            'platform' => (string)$this->platform,
            
            'item_type' => (string)$this->item_type,
            'item_id' => $item_id,

            'category_id' => $this->category ? new CategorySimpleResource($this->category) : null,
            'created_at'  => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ] + $locales;
    }
}
