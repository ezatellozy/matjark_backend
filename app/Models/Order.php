<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function orderPriceDetail()
    {
        return $this->hasOne(OrderPriceDetail::class, 'order_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function flashSaleOrders()
    {
        return $this->hasMany(FlashSaleOrder::class, 'order_id');
    }

    public function offerOrders()
    {
        return $this->hasMany(OfferOrder::class, 'order_id');
    }

    public function orderCoupon()
    {
        return $this->hasOne(OrderCoupon::class, 'order_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function returnOrder()
    {
        return $this->hasOne(ReturnOrder::class, 'order_id');
    }

    public function setOrderStatusTimesAttribute($value)
    {
        $status = $this->order_status_times ? json_decode($this->attributes['order_status_times'], true) : [];
        $status[] = $value;
        $this->attributes['order_status_times'] = json_encode($status);
    }


    public function getQrAttribute()
    {
        return isset($this->attributes['qr']) && $this->attributes['qr'] ? asset('storage/images/bill/'.$this->attributes['qr']) : null;
    }
}
