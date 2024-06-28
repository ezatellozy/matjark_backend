<?php

namespace App\Http\Resources\Api\Provider\Client;

use App\Http\Resources\Api\Provider\City\CityResource;
use App\Http\Resources\Api\Provider\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleClientResource extends JsonResource
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
            'id'                   => (int) $this->id,
            'fullname'             => (string) $this->fullname,
            'phone_code'           => (string) $this->phone_code,
            'phone'                => (string) $this->phone,
            'email'                => (string) $this->email,
            'avatar'               => (string) $this->avatar,
            'country'              => optional($this->profile)->country_id ? new CountryResource($this->profile->country) : null,
            'city'                 => optional($this->profile)->city_id ? new CityResource($this->profile->city) : null,
            'is_admin_active_user' => (bool) $this->is_admin_active_user,
            'is_active'            => (bool) $this->is_active,
                                    'order_count'   => $this->orders()->count(),

            'created_at'           => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
