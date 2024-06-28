<?php

namespace App\Http\Resources\Api\Help;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'id' => (int)$this->id,
            'name' => (string)$this->name,
            'phone_code' =>  (string)$this->phone_code,
            'phone_limit' => $this->phone_number_limit,
            'flag' => $this->image,
        ];
    }
}
