<?php

namespace App\Http\Resources\Api\Website\Address;

use App\Http\Resources\Api\Help\{CityResource, CountryResource};
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
            'full_name' => (string)$this->full_name,
            'phone_code' =>  (string)$this->phone_code,
            'phone' => (string)$this->phone,
            'phone_code' =>  (string)$this->phone_code,
            'phone' => (string)$this->phone,
            'name' => (string) $this->name,
            'country' =>  new CountryResource($this->country),
            'city' =>  new CityResource($this->city),
            'lat' => (double) $this->lat,
            'lng' => (double) $this->lng,
            'desc' => (string)$this->desc,
            'location_description' => (string) $this->location_description,
            'postal_code' =>(string)$this->postal_code,
            'is_default' => (bool)$this->is_default,
            'district' => $this->district


        ];
    }
}
