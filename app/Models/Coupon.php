<?php

namespace App\Models;

use App\Observers\CouponObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $dates = ['start_at', 'end_at'];

    protected $casts = [
        'apply_ids' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();
        Coupon::observe(CouponObserver::class) ;
    }

    public function getImageAttribute()
    {
        $image = $this->media()->exists() ? 'storage/images/coupons/'.$this->media()->first()->media : 'dashboardAssets/images/banner/banner-2.jpg';

        return asset($image);
    }

    public function media()
    {
    	return $this->morphOne(AppMedia::class, 'app_mediaable');
    }
}
