<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyToGetOffer extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'buy_apply_ids' => 'json',
        'get_apply_ids' => 'json'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
