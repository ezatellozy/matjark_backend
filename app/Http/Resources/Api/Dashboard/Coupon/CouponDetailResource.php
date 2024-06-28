<?php

namespace App\Http\Resources\Api\Dashboard\Coupon;

use App\Http\Resources\Api\Dashboard\Order\SimpleOrderResource;
use App\Models\Order;
use App\Models\OrderCoupon;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponDetailResource extends JsonResource
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
            'id'                => (int) $this->id,
            'image'             => (string) $this->image,
            'code'              => (string) $this->code,
            'start_at'          => $this->start_at ? $this->start_at->format('Y-m-d') : null,
            'end_at'            => $this->end_at ? $this->end_at->format('Y-m-d') : null,
            'start_time'           => $this->start_at ? $this->start_at->format('H:i') : null,
            'end_time'             => $this->end_at ? $this->end_at->format('H:i') : null,
            'is_active'         => (bool) $this->is_active,
            'discount_type'     => (string) $this->discount_type,
            'discount_amount'   => (float) $this->discount_amount,
            'max_discount'      => (int) $this->max_discount,
            'max_used_num'      => (int) $this->max_used_num,
            'max_used_for_user' => (int) $this->max_used_for_user,
            'addtion_options'   => (string) $this->addtion_options,
            'applly_coupon_on'  => (string) $this->applly_coupon_on,
            'num_of_used' => (int)$this->num_of_used,
            'remain_used' => $this->max_used_num - $this->num_of_used,
            'created_at'        => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'order_count_use_coupon' => OrderCoupon::where('coupon_id', $this->id)->whereHas('order', function ($q) {
                $q->where('status', 'admin_delivered');
            })->count(),
            // 'order_data_use_coupon'  => OrderCoupon::where('coupon_id', $this->id)->whereHas('order', function ($q) {
            //     $q->where('status', 'admin_delivered');
            // })->count() > 0 ? SimpleOrderResource::collection(Order::where('status', 'admin_delivered')->whereHas('orderCoupon', function ($q) {
            //     $q->where('coupon_id', $this->id);
            // })->get()) : [],
        ];
    }
}
