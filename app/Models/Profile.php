<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    protected $dates = ['last_login_at'];


    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function creator()
    {
    	return $this->belongsTo(User::class,'added_by_id');
    }

    public function city()
    {
    	return $this->belongsTo(City::class);
    }

    public function country()
    {
    	return $this->belongsTo(Country::class);
    }

}
