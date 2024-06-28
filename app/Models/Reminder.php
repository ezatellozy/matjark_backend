<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $guarded = ['id','created_at','updated_at','deleted_at'];

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function flash_sale_product() {
        return $this->belongsTo(FlashSaleProduct::class,'flash_sale_product_id');
    }
}
