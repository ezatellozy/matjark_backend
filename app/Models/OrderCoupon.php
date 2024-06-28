<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCoupon extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public function  order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
