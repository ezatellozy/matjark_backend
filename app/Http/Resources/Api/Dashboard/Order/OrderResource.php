<?php

namespace App\Http\Resources\Api\Dashboard\Order;

use App\Http\Resources\Api\Dashboard\Address\AddressResource;
use App\Http\Resources\Api\Dashboard\Admin\AdminResource;
use App\Http\Resources\Api\Dashboard\Client\ClientResource;
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
            'pay_type_trans'      => trans('app.pay_types.'.$this->pay_type),
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
            'qr'                                => $this->qr,
            'seller_name'       => @$this->seller_name ,
            'tax_number'        => @$this->tax_number ,
            'seller_trand_name' => setting('seller_trand_name') ? setting('seller_trand_name') : null,
            'seller_address'    => setting('seller_address') ? setting('seller_address') : null,
            'seller_commercial_record'  => setting('seller_commercial_record')? setting('seller_commercial_record') : null,
            'created_at'     => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'order_status'  => [
                [
                    'key' => 'pending',
                    'value' => trans('app.messages.status.pending'),
                    'desc' => trans('app.messages.status.desc.pending'),
                    'status' =>    orderStatus('pending', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/pending.svg'),

                ],
                [

                    'key' => 'admin_accept',
                    'value' => trans('app.messages.status.admin_accept'),
                    'desc' => trans('app.messages.status.desc.admin_accept'),
                    'status' =>     orderStatus('admin_accept', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/shipment.svg'),

                ],
                [

                    'key' => 'admin_shipping',
                    'value' => trans('app.messages.status.admin_shipping'),
                    'desc' => trans('app.messages.status.desc.admin_shipping'),
                    'status' =>    orderStatus('admin_shipping', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/shipping.svg'),

                ],
                [

                    'key' => 'admin_delivered',
                    'value' => trans('app.messages.status.admin_delivered'),
                    'desc' => trans('app.messages.status.desc.admin_delivered'),
                    'status' =>     orderStatus('admin_delivered', $this->status, $this->order_status_times),
                    'icon' => asset('dashboardAssets/order_icons/completed.svg'),

                ],
                // [

                //     'key' => 'client_cancel',
                //     'value' => trans('app.messages.status.client_cancel'),
                //     'desc' => trans('app.messages.status.desc.client_cancel'),
                //     'status' =>     orderStatus('client_cancel', $this->status, $this->order_status_times),
                //     'icon' => asset('dashboardAssets/order_icons/cancel.png'),
                // ],
            ],

        ];
    }
}
