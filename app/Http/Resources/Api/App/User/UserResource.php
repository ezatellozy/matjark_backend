<?php

namespace App\Http\Resources\Api\App\User;

use App\Http\Resources\Api\Help\{CityResource,CountryResource};
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'fullname' => (string)$this->fullname,
            'phone_code' => (string)$this->phone_code,
            'phone' => (string)$this->phone,
            'email' => (string)$this->email,
            'avatar' => (string)$this->avatar,
            'country' => optional($this->profile)->country_id ? new  CountryResource($this->profile->country) : null,
            'city' => optional($this->profile)->city_id ? new CityResource($this->profile->city) : null,
            'is_allow_notification' =>(bool)optional($this->profile)->is_allow_notification,
            'token' => $this->when($this->token,$this->token),

        ];
    }
}
