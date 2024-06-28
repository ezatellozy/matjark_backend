<?php

namespace App\Models;

use App\Observers\OrderRateObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRate extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected static function boot()
    {
        parent::boot();
        OrderRate::observe(OrderRateObserver::class);
    }

    public function productDetail()
    {
        return $this->belongsTo(ProductDetails::class, 'product_detail_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function rateImages()
    {
        return $this->hasMany(RateImages::class, 'rate_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getImagesAttribute()
    {
        $images = $this->rateImages()->get();
        $arr = [];
        $i = 0;
        if ($images->count() > 0) {

            foreach ($images as $value) {
                $i++;
                $image = $value->media  != null ? 'storage/images/rateImages/' . $value->media : null;
                $arr[$i]['id'] =  $value->id;
                $arr[$i]['image'] =  $value->media != null ?  $value->media : null;
                $arr[$i]['url'] =  $image != null ? asset($image) : null;
            }
        }
        return $arr;
    }
}
