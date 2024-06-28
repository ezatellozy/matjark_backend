<?php

namespace App\Http\Resources\Api\App\Address;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleAddressResource extends JsonResource
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
            'name' => (string) $this->name,
            'lat' => (double) $this->lat,
            'lng' => (double) $this->lng,
            'location_description' => (string) $this->location_description,
        ];
    }
}
