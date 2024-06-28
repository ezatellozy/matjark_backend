<?php

namespace App\Http\Resources\Api\App\Order;

use App\Http\Resources\Api\App\ReturnOrder\ReturnOrderResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleOrderResource extends JsonResource
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
            'status' => (string)$this->status,
            'status_trans' =>  (string)trans('app.messages.status.' . $this->status),
            'desc_status_trans' =>  (string)trans('app.messages.status.desc.' . $this->status),
            'icon' => OrderStatusIcon($this->status),
            'distance' => (float)$this->distance,
            'total_price' => $this->orderPriceDetail? (float)$this->orderPriceDetail->total_price: 0,
            // 'items' =>  ImageProductResource::collection( $this->orderProducts),
            'items' => OrderProductResource::collection($this->orderProducts),
            'created_at' => $this->created_at->format('Y-m-d'),
            'comment' => (string)$this->comment,
            'count_of_products' => $this->orderProducts()->sum('quantity'),
            'have_return_order' =>  $this->returnOrder != null ? true : false,
            'return_order' =>  $this->returnOrder != null ? new ReturnOrderResource($this->returnOrder) : null,

            'qr'                                => $this->qr,


        ];
    }
}
