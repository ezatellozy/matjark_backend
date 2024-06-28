<?php

namespace App\Http\Resources\Api\Website\Meta;


use Illuminate\Http\Resources\Json\JsonResource;

class MetaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
    //   $locales = [];

    //     foreach (config('translatable.locales') as $locale) {
    //         $locales[$locale]['meta_tag'] = $this->translate($locale)->meta_tag;
    //         $locales[$locale]['meta_title'] = $this->translate($locale)->meta_title;
    //         $locales[$locale]['meta_description'] = $this->translate($locale)->meta_description;
    //     }

        return[
            'id'          => (int) $this->id,
            'meta_tag' => $this->meta_tag,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_canonical_tag' => $this->meta_canonical_tag
        ]
        
        // + $locales
        
        ;
    }
}