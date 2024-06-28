<?php

namespace App\Http\Resources\Api\Provider\Size;

use App\Http\Resources\Api\Provider\Category\CategorySimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SizeResource extends JsonResource
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
            $locales[$locale]['tag']  = $this->translate($locale)->tag;
            $locales[$locale]['desc'] = $this->translate($locale)->desc;
        }

        return [
            'id'         => (int) $this->id,
            'name'       => (string) $this->name,
            'tag'        => (string) $this->tag,
            'desc'        => (string) $this->desc,

            'categories' => CategorySimpleResource::collection($this->categories),
            'ordering'   => (int) $this->ordering
        ] + $locales;
    }
}
