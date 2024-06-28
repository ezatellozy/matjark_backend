<?php

namespace App\Models;

use App\Observers\OfferObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model implements TranslatableContract
{
    use Translatable;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['name', 'desc'];
    protected $dates = ['start_at', 'end_at'];

    protected static function boot()
    {
        parent::boot();
        Offer::observe(OfferObserver::class);
    }

    public function getImageAttribute()
    {
        $media = $this->media()->first();

        $image = $media ? 'storage/images/offers/' . $media->media : 'dashboardAssets/images/cover/consult-cover2.jpg';

        return asset($image);
    }

    public function media()
    {
        return $this->morphOne(AppMedia::class, 'app_mediaable');
    }

    public function discountOfOffer()
    {
        return $this->hasOne(DiscountOfOffer::class);
    }

    public function buyToGetOffer()
    {
        return $this->hasOne(BuyToGetOffer::class);
    }

    public function getAppImageEnAttribute()
    {
        $media = $this->media()->where('option', 'app_image_en')->first();

        $image = $media ? 'storage/images/offers/' . $media->media : null;

        return $image == null ?null:asset($image);
    }
    public function getAppImageArAttribute()
    {
        $media = $this->media()->where('option', 'app_image_ar')->first();

        $image = $media ? 'storage/images/offers/' . $media->media : null;

        return $image == null ?null:asset($image);
    }
    public function getWebImageEnAttribute()
    {
        $media = $this->media()->where('option', 'web_image_en')->first();

        $image = $media ? 'storage/images/offers/' . $media->media : null;

        return $image == null ?null:asset($image);
    }
    public function getWebImageArAttribute()
    {
        $media = $this->media()->where('option', 'web_image_ar')->first();

        $image = $media ? 'storage/images/offers/' . $media->media : null;

        return $image == null ?null:asset($image);
    }
}
