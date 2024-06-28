<?php

namespace App\Models;

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

class City extends Model implements TranslatableContract
{
    use Translatable, SpatialTrait;
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    public $translatedAttributes = ['name'];
    protected $spatialFields = [
        'location',
        'area'
    ];

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

    public function country()
    {
    	return $this->belongsTo(Country::class);
    }

    public function users()
    {
    	return $this->hasManyThrough(User::class,Profile::class,'city_id','id','id','user_id');
    }

    public function clients()
    {
    	return $this->hasManyThrough(User::class,Profile::class,'city_id','id','id','user_id')->where('users.user_type','client');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

}
