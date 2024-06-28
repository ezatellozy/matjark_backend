<?php

namespace App\Http\Resources\Api\App\Help;

use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
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
            'name'     => (string) $this->name,
            'hex'      => (string) $this->hex,
            'image'    => (string) $this->image,
        ];
    }
}
