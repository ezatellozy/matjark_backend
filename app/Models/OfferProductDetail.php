<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferProductDetail extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $table = 'offer_product_detail';
    
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }
}
