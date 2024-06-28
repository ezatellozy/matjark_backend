<?php

namespace App\Http\Resources\Api\Dashboard\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class SelectClientResource extends JsonResource
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
            'id'       => (int) $this->id,
            'fullname' => (string) $this->fullname,
        ];
    }
}
