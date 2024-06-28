<?php

namespace App\Http\Resources\Api\Provider\Order;

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
            'id'                                => (int) $this->id,
            'status'                            => (string) $this->status,
            'status_trans'                      => (string) trans('app.messages.status.' . $this->status),
            'is_payment'                        => $this->is_payment == 'paid' ? true : false,
            'distance'                          => (float) $this->distance,
            'comment' => (string)$this->comment,
            'total_price'                       => (float) $this->orderPriceDetail->total_price,
            'total_price_after_return_products' => (float) $this->orderPriceDetail->total_price_after_return_products,
            'total_quantity_product'            => (int) $this->orderProducts()->sum('quantity'),
            'created_at'                        => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
