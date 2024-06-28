<?php

namespace App\Http\Resources\Api\Provider\Order;

use App\Http\Resources\Api\Provider\Address\AddressResource;
use App\Http\Resources\Api\Provider\Admin\AdminResource;
use App\Http\Resources\Api\Provider\Client\ClientResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id'                  => (int) $this->id,
            'is_payment'          => $this->is_payment == 'paid' ? true : false,
            'transactionId'       => (string) $this->transaction_id,
            'pay_type'            => (string) $this->pay_type,
            'status'              => (string) $this->status,
            'status_trans'        => (string) trans('app.messages.status.' . $this->status),
            'address'             => $this->address ? new AddressResource($this->address) : null,
            'address_note'        => (string) $this->address_note,
            'admin_reject_reason' => (string) $this->admin_reject_reason,
            'user_cancel_reason'  => (string) $this->user_cancel_reason,
            'note'                => (string) $this->user_cancel_reason,
            'distance'            => (float) $this->distance,
            'comment' => (string)$this->comment,
            'user_cancel_reason'  => $this->user_cancel_reason != null ?(string)$this->user_cancel_reason:null, 
            'admin'               => $this->admin ? new AdminResource($this->admin) : null,
            'client'              => $this->client ? new ClientResource($this->client) : null,
            'price_details'       => $this->orderPriceDetail ? new OrderPriceDetailResource($this->orderPriceDetail) : null,
            'products'            => OrderProductResource::collection($this->orderProducts),
            'marchent_order_id' => $this->marchent_order_id != null ?  (string)$this->marchent_order_id :null ,
        ];
    }
}
