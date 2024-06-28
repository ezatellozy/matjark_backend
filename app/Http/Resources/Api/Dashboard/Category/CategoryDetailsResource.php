<?php

namespace App\Http\Resources\Api\Dashboard\Category;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;

class CategoryDetailsResource extends JsonResource
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
            $locales[$locale]['meta_canonical_tag'] = $this->translate($locale)->meta_canonical_tag;
            $locales[$locale]['desc'] = $this->translate($locale)->desc;
            // $locales[$locale]['keywords'] = $this->translate($locale)->keywords;
            if($this->metas()->count()){
                $locales[$locale]['meta_tag'] = $this->metas?$this->metas->translate($locale)->meta_tag: null;
                $locales[$locale]['meta_title'] = $this->metas?$this->metas->translate($locale)->meta_title: null;
                $locales[$locale]['meta_description'] = $this->metas?$this->metas->translate($locale)->meta_description: null;
            }
        }

        return [
            'id'             => (int) $this->id,
            'name'           => (string) $this->name,
            'slug'           => (string) $this->slug,
            'meta_canonical_tag' => (string) $this->meta_canonical_tag,
            // 'desc'           => (string) $this->desc,
            // 'keywords'       => (string) $this->keywords,
            'image'          => $this->image,
            'image_alt_ar'   => $this->media->alt_ar??null,
            'image_alt_en'   => $this->media->alt_en??null,
            'is_active'      => (bool) $this->is_active,
            'ordering'       => (int) $this->ordering,
            'position'       => (string) $this->position,
            'children_count' => (int) count($this->children),
            'children'       => CategoryResource::collection($this->children),
            'parent'         => $this->parent ? new ParentCategoryResource($this->parent) : null,
            'meta_canonical_tag' => $this->metas->meta_canonical_tag?? null
            // 'meta_data' => $this->metas? new MetaResource($this->metas): null
        ] + $locales;
    }
}
