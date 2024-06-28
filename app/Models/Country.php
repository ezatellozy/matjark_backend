<?php

namespace App\Models;

use App\Observers\CountryObserver;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Grimzy\LaravelMysqlSpatial\{
    Eloquent\SpatialTrait,
    Types\Point,
    Types\Polygon,
    Types\LineString
};

class Country extends Model implements TranslatableContract
{
    use Translatable, SpatialTrait;
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    
    public $translatedAttributes = ['name', 'nationality'];

    protected $spatialFields = [
        'location',
        'area'
    ];

    protected static function boot()
    {
        parent::boot();
        Country::observe(CountryObserver::class) ;
    }

    public function setAreaAttribute($value_arr)
    {
        if ($value_arr) {
            $points = [];
            foreach ($value_arr as $value) {
                $points[] = new Point($value['lat'], $value['lng']);
            }
            $first_point = $points[0];
            $points[] = $first_point;
            $this->attributes['area'] = new Polygon([new LineString($points)]);
        }
    }

    public function getAreaAttribute()
    {
        $points = [];
        if (isset($this->attributes['area'])) {
            foreach ($this->attributes['area'] as $key => $value) {
                foreach ($value as $key => $coordinate) {
                    $points[] = ['lat' => $coordinate->getLat(), 'lng' => $coordinate->getLng()];
                }
            }
        }

        return $points;
    }

    public function getImageAttribute()
    {
        $image = $this->media()->exists() ? 'storage/images/countries/'.$this->media()->first()->media : 'dashboardAssets/images/cover/cover_sm.png';

        return asset($image);
    }

    // Relations
    // ========================= Image ===================
    public function media()
    {
    	return $this->morphOne(AppMedia::class,'app_mediaable');
    }

    public function cities()
    {
    	return $this->hasMany(City::class);
    }

    public function users()
    {
    	return $this->hasManyThrough(User::class,Profile::class);
    }
}
