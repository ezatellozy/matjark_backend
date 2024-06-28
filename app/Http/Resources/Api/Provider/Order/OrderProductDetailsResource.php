<?php

namespace App\Http\Resources\Api\Provider\Order;

use App\Http\Resources\Api\Provider\Color\ColorResource;
use App\Http\Resources\Api\Provider\Size\SizeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductDetailsResource extends JsonResource
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
            'id'        => (int) $this->id,
            'price'     => (float) $this->price,
            'currency'  => 'EGP',
            'color'     => $this->color ? new ColorResource($this->color) : null,
            'size'      => $this->size ? new SizeResource($this->size) : null,
            'image'     => $this->image,
            'is_fav'    => false,
            'have_sale' => $this->have_sale,
        ];
    }
}
