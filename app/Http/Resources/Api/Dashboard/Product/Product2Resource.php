<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\Dashboard\Category\CategorySimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;
use App\Http\Resources\Api\Dashboard\CommonQuestion\SimpleCommonQuestionResource;

class Product2Resource extends JsonResource
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
            $locales[$locale]['slug'] = $this->translate($locale)->slug;
            $locales[$locale]['desc'] = $this->translate($locale)->desc;
            if($this->metas()->count()){
                $locales[$locale]['meta_tag'] = $this->metas?$this->metas->translate($locale)->meta_tag: null;
                $locales[$locale]['meta_title'] = $this->metas?$this->metas->translate($locale)->meta_title: null;
                $locales[$locale]['meta_description'] = $this->metas?$this->metas->translate($locale)->meta_description: null;
            }
        }

        // if($this->productDetails && $this->productDetails()->count() > 0) {
        //     $media = (($this->productDetails)[0])->media;
        // } else {
        //     $media = [];
        // }

        // ProductDetails2Resource::using(['media' => $media]);

        return [
            'id'              => (int) $this->id,
            'name'            => (string) $this->name,
            'slug'            => (string)$this->slug,
            'desc'            => (string) $this->desc,
            'code'            => $this->code,
            'size_guide' => $this->size_guide,
            'rate_avg'        => (double) $this->rate_avg,
            'is_active'       => (bool) $this->is_active,
            'ordering'        => (int) $this->ordering,
            'media' => ProductMediaResource::collection($this->media->whereNull('option')),
            'categories'      => CategorySimpleResource::collection($this->categories),
            'main_category'   => count($this->categories) > 0 ? new CategorySimpleResource(root($this->categories->first())) : null,
            'product_details' => ProductDetails2Resource::collection($this->productDetails),
            'created_at'      => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'common_questions' => SimpleCommonQuestionResource::collection($this->commonQuestions),
            'meta_canonical_tag' => $this->metas()->count()?$this->metas->meta_canonical_tag:null
            // 'meta_data' => $this->metas? new MetaResource($this->metas): null
        ] + $locales;
    }
}
