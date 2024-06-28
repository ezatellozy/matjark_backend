<?php

namespace App\Http\Resources\Api\Provider\About;

use Illuminate\Http\Resources\Json\JsonResource;

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
        ] +$data;
    }
}
