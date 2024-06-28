<?php

namespace App\Http\Resources\Api\Website\Home;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Website\Meta\MetaResource;

class SimpleCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => (int)$this->id,
            'name' => (string)$this->name,
            'slug' => (string)$this->slug,
            'meta_tags' => $this->metas? MetaResource::make($this->metas): []
        ];
    }
}
