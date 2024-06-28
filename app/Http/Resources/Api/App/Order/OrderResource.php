<?php

namespace App\Http\Resources\Api\App\Order;

use App\Http\Resources\Api\App\Address\AddressResource;
use App\Http\Resources\Api\App\ReturnOrder\ReturnOrderResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Stringable;

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
            'id' => (int)$this->id,
            'is_payment' => (string)$this->is_payment,
            'is_payment_trans' => (string)trans('app.messages.' . $this->is_payment),
            'pay_type' => (string)$this->pay_type,
            'pay_type_trans' => trans('app.pay_types.'.$this->pay_type),
            'status' => (string)$this->status,
            'status_trans' =>  (string)trans('app.messages.status.' . $this->status),
            'desc_status_trans' =>  (string)trans('app.messages.status.desc.' . $this->status),
            'icon' => OrderStatusIcon($this->status),

            'have_return_order' =>  $this->returnOrder != null ? true : false,
            'return_order' =>  $this->returnOrder != null ? new ReturnOrderResource($this->returnOrder) : null,
            'comment' => (string)$this->comment,
            'address' => new AddressResource($this->address),
            'distance' => (float)$this->distance,
            'price_details' =>  new OrderPriceDetailResource($this->orderPriceDetail),
            'items' => OrderProductResource::collection($this->orderProducts),
            'user_cancel_reason'  => $this->user_cancel_reason != null ? (string)$this->user_cancel_reason : null,
            'order_status'  => [
                [
                    'key' => 'pending',
                    'value' => trans('app.messages.status.pending'),
                    'desc' => trans('app.messages.status.desc.pending'),
                    'status' =>    orderStatus('pending', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/pending.png'),

                ],
                [

                    'key' => 'admin_accept',
                    'value' => trans('app.messages.status.admin_accept'),
                    'desc' => trans('app.messages.status.desc.admin_accept'),
                    'status' =>     orderStatus('admin_accept', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/shipping.png'),

                ],
                [

                    'key' => 'admin_shipping',
                    'value' => trans('app.messages.status.admin_shipping'),
                    'desc' => trans('app.messages.status.desc.admin_shipping'),
                    'status' =>    orderStatus('admin_shipping', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/shipping.png'),

                ],
                [

                    'key' => 'admin_delivered',
                    'value' => trans('app.messages.status.admin_delivered'),
                    'desc' => trans('app.messages.status.desc.admin_delivered'),
                    'status' =>     orderStatus('admin_delivered', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/completed.png'),
                ],
                // [

                //     'key' => 'client_cancel',
                //     'value' => trans('app.messages.status.client_cancel'),
                //     'desc' => trans('app.messages.status.desc.client_cancel'),
                //     'status' =>     orderStatus('client_cancel', $this->status, $this->order_status_times),
                //     'icon' => asset('dashboardAssets/order_icons/cancel.png'),
                // ],
            ],
            'qr'                                => $this->qr,

        ];
    }
}
