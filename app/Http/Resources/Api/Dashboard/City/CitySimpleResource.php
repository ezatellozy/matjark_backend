<?php

namespace App\Http\Resources\Api\Dashboard\City;

use Illuminate\Http\Resources\Json\JsonResource;

class CitySimpleResource extends JsonResource
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
            'id'             => (int) $this->id,
            'name'           => (string) $this->name,
            'short_name'     => (string) $this->short_name,
            'slug'           => (string) $this->slug,
            'postal_code'    => (int) $this->postal_code,
            'is_shapping'    => (bool) $this->is_shapping,
            'shipping_price' => (double) $this->shipping_price,
            'created_at'     => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
