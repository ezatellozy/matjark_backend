<?php

namespace App\Http\Resources\Api\Dashboard\About;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;

class AboutResource extends JsonResource
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
            
            $data[$locale]['title'] = $this->translate($locale)->title;
            $data[$locale]['desc'] = $this->translate($locale)->desc;
            $data[$locale]['slug'] = $this->translate($locale)->slug;

            if($this->metas){
                $data[$locale]['meta_tag'] = $this->metas?$this->metas->first()->translate($locale)->meta_tag: null;
                $data[$locale]['meta_title'] = $this->metas?$this->metas->first()->translate($locale)->meta_title: null;
                $data[$locale]['meta_description'] = $this->metas?$this->metas->first()->translate($locale)->meta_description: null;
            }
            // dump(      $this->translate($locale)->title);
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
            'meta_canonical_tag' => $this->metas->meta_canonical_tag?? null
        ] +$data;
    }
}
