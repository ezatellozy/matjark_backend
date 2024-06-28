<?php

namespace App\Http\Resources\Api\App\Order;

use App\Http\Resources\Api\App\Address\AddressResource;
use App\Http\Resources\Api\App\ReturnOrder\ReturnOrderResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Stringable;

class OrderStatusResource extends JsonResource
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
            'order_status'  => [
                [
                    'key' => 'pending',
                    'value' => trans('app.messages.status.pending'),
                    'desc' => trans('app.messages.status.desc.pending'),
                    'status' =>    orderStatusV2('pending', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/pending.png'),

                ],
                [

                    'key' => 'admin_accept',
                    'value' => trans('app.messages.status.admin_accept'),
                    'desc' => trans('app.messages.status.desc.admin_accept'),
                    'status' =>     orderStatusV2('admin_accept', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/shipping.png'),

                ],
                [

                    'key' => 'admin_shipping',
                    'value' => trans('app.messages.status.admin_shipping'),
                    'desc' => trans('app.messages.status.desc.admin_shipping'),
                    'status' =>    orderStatusV2('admin_shipping', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/shipping.png'),

                ],
                [

                    'key' => 'admin_delivered',
                    'value' => trans('app.messages.status.admin_delivered'),
                    'desc' => trans('app.messages.status.desc.admin_delivered'),
                    'status' =>     orderStatusV2('admin_delivered', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/completed.png'),
                ],
                [

                    'key' => 'client_cancel',
                    'value' => trans('app.messages.status.client_cancel'),
                    'desc' => trans('app.messages.status.desc.client_cancel'),
                    'status' =>     orderStatusV2('client_cancel', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/cancel.png'),
                ],
            ],
            'bill' => [
                ''    
            ]
        ];
    }
}
