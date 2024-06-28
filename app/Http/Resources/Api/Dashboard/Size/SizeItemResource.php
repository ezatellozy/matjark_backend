<?php

namespace App\Http\Resources\Api\Dashboard\Size;

use App\Http\Resources\Api\Dashboard\Category\CategorySimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SizeItemResource extends JsonResource
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
            'id'         => (int) $this->id,
            'name'       => (string) $this->name,
            'tag'        => (string) $this->tag,
        ];
    }
}
