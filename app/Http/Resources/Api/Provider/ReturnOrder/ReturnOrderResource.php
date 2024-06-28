<?php

namespace App\Http\Resources\Api\Provider\ReturnOrder;

use App\Http\Resources\Api\Provider\Client\SimpleClientResource;
use App\Http\Resources\Api\Provider\Order\SimpleOrderResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnOrderResource extends JsonResource
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
            "id"           => (int) $this->id,
            "image"        => $this->images,
            "user"         => $this->user ? new SimpleClientResource($this->user) : null,
            "admin"        => $this->admin ? new SimpleClientResource($this->admin) : null,
            "status"       => (string) $this->status,
            'status_trans' => (string) trans('app.return_orders.' . $this->status),
            "note"         => (string) $this->note,
            "order"        => $this->order ? new SimpleOrderResource($this->order) : null,
            "products"     => ReturnOrderProductResource::collection($this->returnOrderProducts),
            "created_at"   => (string) $this->created_at->format('Y-m-d'),
        ];
    }
}
