<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlashSale extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['start_at', 'end_at'];

    public function flashSaleProducts()
    {
        return $this->hasMany(FlashSaleProduct::class, 'flash_sale_id');
    }
}
