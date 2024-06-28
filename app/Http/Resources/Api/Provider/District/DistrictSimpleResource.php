<?php

namespace App\Http\Resources\Api\Provider\District;

use Illuminate\Http\Resources\Json\JsonResource;

class DistrictSimpleResource extends JsonResource
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
            'id'                        => $this->id,
            'uuid'                      => $this->uuid,
            'name'                      => $this->name,
            'slug'                      => $this->slug,
            'location'                  => $this->location,
            'is_available_for_orders'   => (bool)$this->is_available_for_orders,
            'created_at'                => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
