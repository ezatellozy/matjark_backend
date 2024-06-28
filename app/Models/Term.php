<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\TermObserver;

class Term extends Model
{
    use Translatable;

    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    public $translatedAttributes = ['title', 'desc', 'slug'];

    protected static function boot()
    {
        parent::boot();
        Term::observe(TermObserver::class) ;
    }

    public function media()
    {
    	return $this->morphOne(AppMedia::class,'app_mediaable');
    }

    public function getImagesAttribute()
    {
        $images = [];
        $this->media()->each(function($img, $key) use(&$images) {
            $images[$img->option] = asset('storage/images/terms/'.$img->media);
            return $images;
        });
        return $images;
    }

    public function getImageAttribute()
    {
        $media = $this->media()->where('option', app()->getLocale())->first();
        $image = $media ? 'storage/images/terms/' . $media->media : "dashboardAssets/global/images/cover/consult-cover2.jpg";
        return \asset($image);
    }
}
