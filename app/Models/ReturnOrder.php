<?php

namespace App\Models;

use App\Observers\ReturnOrderObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrder extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();
        ReturnOrder::observe(ReturnOrderObserver::class);
    }

    public function getImagesAttribute()
    {
        $images = [];
        $this->returnOrderImages()->each(function($img, $key) use(&$images) {
            $images[$key]['id']    = $img->id;
            $images[$key]['image'] = asset('storage/images/return_orders/'.$img->media);
            return $images;
        });
        return $images;
    }

    public function media()
    {
        return $this->morphOne(AppMedia::class, 'app_mediaable');
    }

    public function returnOrderProducts()
    {
        return $this->hasMany(ReturnOrderProduct::class, 'return_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function returnOrderImages()
    {
        return $this->hasMany(ReturnOrderImage::class, 'return_order_id');
    }
}
