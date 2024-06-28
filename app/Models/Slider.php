<?php

namespace App\Models;

use App\Observers\SliderObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['name', 'desc', 'slug', 'link_name'];

    protected static function boot()
    {
        parent::boot();
        Slider::observe(SliderObserver::class) ;
    }

    public function media()
    {
    	return $this->morphOne(AppMedia::class,'app_mediaable');
    }

    public function getImagesAttribute()
    {
        $images = [];

        $this->media()->each(function($img, $key) use(&$images) {
            $images[$img->option] = asset('storage/images/sliders/'.$img->media);
            return $images;
        });

        return $images;
    }

    public function getImageAttribute()
    {
        //$media = $this->media()->where('option', app()->getLocale())->first();
        $media = $this->media()->first();
        $image = $media ? 'storage/images/sliders/' . $media->media : "dashboardAssets/global/images/cover/consult-cover2.jpg";

        return \asset($image);
    }
    
    public function getCropImageAttribute()
    {
        //$media = $this->media()->where('option', app()->getLocale())->first();
        $media = $this->media()->first();
        $image = $media ? 'storage/images/sliders/crop/' . $media->media : "dashboardAssets/global/images/cover/consult-cover2.jpg";

        return \asset($image);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
