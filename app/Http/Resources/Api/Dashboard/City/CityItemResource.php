<?php

namespace App\Http\Resources\Api\Dashboard\City;

use Illuminate\Http\Resources\Json\JsonResource;

class CityItemResource extends JsonResource
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
            'name'           => (string) $this->name,
        ];
    }
}
