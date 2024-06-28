<?php

namespace App\Http\Resources\Api\Dashboard\Size;

use App\Http\Resources\Api\Dashboard\Category\CategorySimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SizeAdminResource extends JsonResource
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
            'id'                => $this->size_id,
            'product_detail_id' => $this->id,
            'name'              => (string) @$this->size->name,
            'tag'               => (string) @$this->size->tag,
            'price'             => $this->price,
            'quantity'          => $this->quantity,
        ];
    }
}
