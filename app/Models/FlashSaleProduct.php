<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlashSaleProduct extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function  product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function  productDetail()
    {
        return $this->belongsTo(ProductDetails::class, 'product_detail_id');
    }

    public function  flashSale()
    {
        return $this->belongsTo(FlashSale::class, 'flash_sale_id');
    }


    public function reminderFlashSales()
    {
        return $this->hasMany(Reminder::class, 'flash_sale_product_id');
    }
}
