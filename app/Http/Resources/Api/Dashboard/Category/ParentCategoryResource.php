<?php

namespace App\Http\Resources\Api\Dashboard\Category;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;

class ParentCategoryResource extends JsonResource
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
            'id'             => (int) $this->id,
            // 'parent_id'      => $this->parent_id ? (int) $this->parent_id : null,
            'name'           => (string) $this->name,
            'slug'           => (string) $this->slug,
            'desc'           => (string) $this->desc,
            'image'          => $this->image,
            'is_active'      => (bool) $this->is_active,
            'ordering'       => (int) $this->ordering,
            'position'       => (string) $this->position,
            'meta_data' => $this->metas? new MetaResource($this->metas): null
            // 'children_count' => (int) count($this->children),
            // 'parent'         => $this->parent ? new CategoryResource($this->parent) : null,
        ];
    }
}
