<?php

namespace App\Http\Resources\Api\Dashboard\Category;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;

class CategorySimpleNewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return [
        //     'id'       => (int) $this->id,
        //     'name'     => (string) $this->name,
        //     'slug' => (string)$this->slug,
        //     'parent'     => $this->parent_id != null   ? new CategorySimpleResource($this->parent) : null,
        //     'position' => (string) $this->position,
        //     'label'    => (string) $this->name,
        //     'meta_data' => $this->metas? new MetaResource($this->metas): null
        // ];

        return [
            'root_id'     => (int) $this->root->id,
            'category_id' => (int) $this->id
        ];
    }
}
