<?php

namespace App\Http\Resources\Api\Dashboard\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class TreetCategoryResource extends JsonResource
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
            'id'      => (int) $this->id,
            'label'   => (string) $this->name,
           'children' => $this->when($this->children()->count() > 0, TreetCategoryResource::collection($this->children)),
        ];
    }
}
