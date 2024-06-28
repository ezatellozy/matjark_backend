<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPriceDetail extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];


    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }
    public function orderCoupon()
    {
        return   $this->hasOne(OrderCoupon::class, 'order_price_detail_id');
    }
}
