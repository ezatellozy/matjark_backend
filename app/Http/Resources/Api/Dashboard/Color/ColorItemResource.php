<?php

namespace App\Http\Resources\Api\Dashboard\Color;

use Illuminate\Http\Resources\Json\JsonResource;

class ColorItemResource extends JsonResource
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
            'hex'      => $this->hex ? (string) $this->hex : null,
            'image'    => (string) $this->image,
        ];
    }
}
