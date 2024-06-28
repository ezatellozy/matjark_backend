<?php

namespace App\Http\Resources\Api\Provider\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorySimpleResource extends JsonResource
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
            $locales[$locale]['name'] = $this->translate($locale)?->name;
            $locales[$locale]['desc'] = $this->translate($locale)?->desc;
        }

        return [
            'id'       => (int) $this->id,
            'name'     => (string) $this->name,
            'parent'     => $this->parent_id != null   ? new CategorySimpleResource($this->parent) : null,
            'position' => (string) $this->position,
            'label'    => (string) $this->name,
        ] + $locales;
    }
}
