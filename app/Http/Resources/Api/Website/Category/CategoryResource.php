<?php

namespace App\Http\Resources\Api\Website\Category;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Website\Meta\MetaResource;

class CategoryResource extends JsonResource
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
            // 'slug' => Str::snake($this->translate('en')->name),
            'slug' => (string)$this->slug,
            'sub_categories' =>  CategoryResource::collection($this->children),
            'meta_tags' => MetaResource::make($this->metas)
        ];
    }
}
