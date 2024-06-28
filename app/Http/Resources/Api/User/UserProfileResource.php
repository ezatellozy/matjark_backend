<?php

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Api\Help\{CityResource,CountryResource};
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'fullname' => $this->fullname,
            'phone' => (string)$this->phone,
            'email' => (string)$this->email,
            'image' => (string)$this->avatar,

            'unread_notifications' => $this->unreadnotifications->count(),

            'user_type' => (string)$this->user_type,
            'token' => $this->when($this->token,$this->token),
            'country' => optional($this->profile)->country_id ? new CountryResource($this->profile->country) : null,
            'city' => optional($this->profile)->city_id ? new CityResource($this->profile->city) : null,

        ];
    }
}
