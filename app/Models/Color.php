<?php

namespace App\Models;

use App\Observers\ColorObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['name'];

    protected static function boot()
    {
        parent::boot();
        Color::observe(ColorObserver::class) ;
    }

    public function getImageAttribute()
    {
        $image = $this->media()->exists() ? asset('storage/images/colors/'.$this->media()->first()->media) : null;

        return $image;
    }

    public function getCropImageAttribute()
    {
        $image = $this->media()->exists() ? asset('storage/images/colors/crop/'.$this->media()->first()->media) : null;

        return $image;
    }

    public function media()
    {
    	return $this->morphOne(AppMedia::class, 'app_mediaable');
    }

    public function products() {
        return $this->hasMany(ProductDetails::class);
    }

}
