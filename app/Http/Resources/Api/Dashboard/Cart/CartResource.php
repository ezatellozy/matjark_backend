<?php

namespace App\Http\Resources\Api\Dashboard\Cart;

use App\Http\Resources\Api\Dashboard\Client\SelectClientResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;

class CartResource extends JsonResource
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
            'id'          => $this->id,
            'is_gest'     => $this->client ? false : true,
            'client'      => SelectClientResource::make($this->client),
            'items_count' => $this->cartProducts()->count(),
        ];
    }
}
