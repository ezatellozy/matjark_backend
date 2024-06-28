<?php

namespace App\Http\Resources\Api\Provider\ReturnOrder;

use App\Http\Resources\Api\Provider\Client\SimpleClientResource;
use App\Http\Resources\Api\Provider\Order\SimpleOrderResource;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexReturnOrderResource extends JsonResource
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
            "id"            => (int) $this->id,
            "images"        => $this->images,
            "user"          => $this->user ? new SimpleClientResource($this->user) : null,
            "status"        => (string) $this->status,
            "order"         => $this->order ? new SimpleOrderResource($this->order) : null,
            "count_product" => (int) count($this->returnOrderProducts),
            "created_at"    => (string) $this->created_at->format('Y-m-d'),
        ];
    }
}
