<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrderProduct extends Model
{
    protected $guarded = ['id','created_at','updated_at','deleted_at'];

    public function productDetail()
    {
        return $this->belongsTo(ProductDetails::class, 'product_detail_id');
    }

    public function returnOrder()
    {
        return $this->belongsTo(ReturnOrder::class);
    }
}
