<?php

namespace App\Models;

use App\Traits\Uuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['read_at'];

    public function getImageAttribute()
    {
        $image = $this->user ? $this->user->avatar : asset('dashboardAssets/images/cover/cover_sm.png');
        return $image;
    }

    public function scopePublished($query)
    {
        $query->where('created_at', '<', Carbon::now());
    }

    public function scopeReadMessages($query)
    {
        $query->whereNotNull('read_at');
    }

    public function scopeUnReadMessages($query)
    {
        $query->whereNull('read_at');
    }

    public function setUserIdAttribute($value)
    {
        if (auth()->guard('api')->check()) {
            $this->attributes['user_id'] = auth('api')->id();
        }
    }

    public function setFullnameAttribute($value)
    {
        if (auth()->guard('api')->check()) {
            $this->attributes['fullname'] = auth()->guard('api')->user()->fullname;
        } else {
            $this->attributes['fullname'] = $value;
        }
    }

    public function setEmailAttribute($value)
    {
        if (auth()->guard('api')->check()) {
            $this->attributes['email'] = auth()->guard('api')->user()->email;
        } else {
            $this->attributes['email'] = $value;
        }
    }

    public function setPhoneAttribute($value)
    {
        if (auth()->guard('api')->check()) {
            $this->attributes['phone'] = auth()->guard('api')->user()->phone;
        } else {
            $this->attributes['phone'] = $value;
        }
    }

    // public function setCvFileAttribute($value)
    // {
    //     if ($value && $value->isValid()) {
    //         if (isset($this->attributes['cv_file']) && $this->attributes['cv_file']) {
    //             if (file_exists(storage_path('app/public/files/job_cvs/'. $this->attributes['cv_file']))) {
    //                 \File::delete(storage_path('app/public/files/job_cvs/'. $this->attributes['cv_file']));
    //             }
    //         }
    //         $cv_file = uploadFile($value,'job_cvs');
    //         $this->attributes['cv_file'] = $cv_file;
    //     }
    // }

    public function setContentAttribute($value)
    {
        if (auth()->guard('api')->check()) {
            // $this->attributes['fullname'] = auth()->guard('api')->user()->fullname;
            $this->attributes['user_id'] = auth('api')->id();
            // $this->attributes['email'] = auth()->guard('api')->user()->email;
            // $this->attributes['phone'] = auth()->guard('api')->user()->phone;
        }
        $this->attributes['content'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ContactReply::class);
    }
}
