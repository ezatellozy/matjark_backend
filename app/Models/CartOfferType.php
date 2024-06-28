<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartOfferType extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }
    public function productDetail()
    {
        return $this->belongsTo(ProductDetails::class, 'product_detail_id');
    }

    public function cartProduct(){
        return $this->belongsTo(CartProduct::class, 'cart_product_id');
    }
}
