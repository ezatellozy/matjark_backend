<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function cartProducts()
    {
        return $this->hasMany(CartProduct::class, 'cart_id');
    }

    public function getTotalPriceAttribute()
    {
        return $this->cartProducts->sum('total_price');
    }
    public function cartOfferTypes(){
        return $this->hasMany(CartOfferType::class , 'cart_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
