<?php

namespace App\Http\Resources\Api\Dashboard\StaticPage;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;

class StaticPageMetaDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
    $data = [];
        foreach (config('translatable.locales') as $locale) {
            $data[$locale]['meta_tag'] = $this->meta_tag;
            $data[$locale]['meta_title'] = $this->meta_title;
            $data[$locale]['meta_description'] = $this->meta_description;
        }
        return [
            'id'         => $this->id,
            'created_at' =>  $this->created_at->format('Y-m-d'),
            'meta_canonical_tag' => $this->meta_canonical_tag,
            'option' => $this->option
        ] +$data;
    }
}