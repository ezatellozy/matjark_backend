<?php

namespace App\Http\Resources\Api\Dashboard\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Client\ClientResource;

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
            'id'                                => (int) $this->id,
            'client'              => $this->client ? new ClientResource($this->client) : null,
            'status'                            => (string) $this->status,
            'status_trans'                      => (string) trans('app.messages.status.' . $this->status),
            'is_payment'                        => $this->is_payment == 'paid' ? true : false,
            'distance'                          => (float) $this->distance,
            'comment' => (string)$this->comment,
            'total_price'                       => $this->orderPriceDetail?(float) $this->orderPriceDetail->total_price:0,
            'total_price_after_return_products' => $this->orderPriceDetail?(float)$this->orderPriceDetail->total_price_after_return_products:0,
            'total_quantity_product'            => (int) $this->orderProducts()->sum('quantity'),
            'created_at'                        => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'qr'                                => $this->qr,
            'pay_type'                          => $this->pay_type,
            'return_order_id'                   => $this->returnOrder ? $this->returnOrder->id : null,
        ];
    }
}
