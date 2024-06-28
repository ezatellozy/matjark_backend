<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\AboutObserver;

class About extends Model
{
    use HasFactory, Translatable;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['title', 'desc'];

    protected static function boot()
    {
        parent::boot();
        About::observe(AboutObserver::class) ;
    }

    public function media()
    {
    	return $this->morphOne(AppMedia::class,'app_mediaable');
    }
    public function metas()
    {
        return $this->morphOne(AboutMetaData::class, 'metable');
    }

    public function getImagesAttribute()
    {
        $images = [];
        $this->media()->each(function($img, $key) use(&$images) {
            $images[$img->option] = asset('storage/images/abouts/'.$img->media);
            return $images;
        });
        return $images;
    }

    public function getImageAttribute()
    {
        $media = $this->media()->where('option', app()->getLocale())->first();
        $image = $media ? 'storage/images/abouts/' . $media->media : "dashboardAssets/global/images/cover/consult-cover2.jpg";
        return \asset($image);
    }
}
