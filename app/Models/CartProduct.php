<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }
    public function productDetail()
    {
        return $this->belongsTo(ProductDetails::class, 'product_detail_id');
    }


    public function flashSaleProduct()
    {
        return $this->belongsTo(FlashSaleProduct::class, 'flash_sale_product_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function getTotalPriceAttribute()
    {
        return $this->attributes['quantity'] * $this->productDetail->price;
    }
    public function cartOfferTypes(){
        return $this->hasMany(CartOfferType::class,'cart_product_id');
    }
}
