<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{

    use SoftDeletes;

    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    
    public function user()
    {
    	return $this->belongsTo(User::class,'user_id');
    }

    public function city()
    {
    	return $this->belongsTo(City::class,'city_id');
    }

    public function country()
    {
    	return $this->belongsTo(Country::class ,'country_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
