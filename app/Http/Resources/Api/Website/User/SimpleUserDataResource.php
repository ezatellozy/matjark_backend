<?php

namespace App\Http\Resources\Api\Website\User;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleUserDataResource extends JsonResource
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
            'avatar' => (string)$this->avatar,
            'is_allow_notification' => (bool)optional($this->profile)->is_allow_notification,
            'token' => $this->when($this->token, $this->token),

        ];
    }
}
