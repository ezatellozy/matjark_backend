<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSaleOrder extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function flash_sale_product()
    {
        return $this->belongsTo(FlashSaleProduct::class, 'flash_sale_product_id');
    }

}
