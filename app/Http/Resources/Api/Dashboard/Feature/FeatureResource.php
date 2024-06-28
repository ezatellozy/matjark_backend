<?php

namespace App\Http\Resources\Api\Dashboard\Feature;

use App\Http\Resources\Api\Dashboard\Category\CategorySimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureResource extends JsonResource
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
        }

        return [
            'id'         => (int) $this->id,
            'name'       => (string) $this->name,
            'is_active'  => (bool) $this->is_active,
            'ordering'   => (int) $this->ordering,
            'categories' => CategorySimpleResource::collection($this->categories),
            'values'     => FeatureValueResource::collection($this->values),
        ] + $locales;
    }
}
