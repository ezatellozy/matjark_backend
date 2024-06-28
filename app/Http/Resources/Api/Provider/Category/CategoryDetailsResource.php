<?php

namespace App\Http\Resources\Api\Provider\Category;

use Illuminate\Http\Resources\Json\JsonResource;

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
            $locales[$locale]['desc'] = $this->translate($locale)->desc;
        }

        return [
            'id'             => (int) $this->id,
            'name'           => (string) $this->name,
            'desc'           => (string) $this->desc,
            'image'          => $this->image,
            'is_active'      => (bool) $this->is_active,
            'ordering'       => (int) $this->ordering,
            'position'       => (string) $this->position,
            'children_count' => (int) count($this->children),
            'children'       => CategoryResource::collection($this->children),
            'parent'         => $this->parent ? new ParentCategoryResource($this->parent) : null,
        ] + $locales;
    }
}
