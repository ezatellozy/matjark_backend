<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Permission extends Model
{
    use Translatable;

    protected $guarded = ['id','created_at','updated_at'];
    public $translatedAttributes = ['title'];

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function getAdminPermissionsAttribute()
    {
        // $admin = 
        return $this->where('back_route_name', 'like', 'admin%')
        ->get();
    }
}