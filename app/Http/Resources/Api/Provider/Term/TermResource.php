<?php

namespace App\Http\Resources\Api\Provider\Term;

use Illuminate\Http\Resources\Json\JsonResource;

class TermResource extends JsonResource
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
            $locales[$locale]['title'] = $this->translate($locale)->title;
            $locales[$locale]['desc'] = $this->translate($locale)->desc;
            $locales[$locale]['slug'] = $this->translate($locale)->slug;
        }

        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'desc'       => $this->desc,
            'slug'       => $this->slug,
            'ordering'   => $this->ordering,
            'images'     => $this->images,
            'image'      => $this->image,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ] + $locales;
    }
}
