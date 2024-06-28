<?php

namespace App\Http\Resources\Api\Dashboard\Address;

use App\Http\Resources\Api\Dashboard\City\CityResource;
use App\Http\Resources\Api\Dashboard\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'id'          => (int) $this->id,
            'full_name'   => (string) $this->full_name,
            'name'        => (string) $this->name,
            'phone_code'  => (string) $this->phone_code,
            'phone'       => (string) $this->phone,
            'country'     => new CountryResource($this->country),
            'city'        => new CityResource($this->city),
            'lat'         => (double) $this->lat,
            'lng'         => (double) $this->lng,
            'postal_code' => (string) $this->postal_code,
            'is_default'  => (bool) $this->is_default,
            'location_description' => (string) $this->location_description,
            'district' => $this->district
        ];
    }
}
