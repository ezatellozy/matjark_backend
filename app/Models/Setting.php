<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = ['id','created_at','updated_at'];

    public function setImageAttribute($value)
    {
        if ($value != null && $value != '') {

            if(! is_string($value) && $value->isValid()) {

                if (isset($this->attributes['website_logo']) && $this->attributes['website_logo']) {
                    if (file_exists(storage_path('app/public/images/setting/'. $this->attributes['website_logo']))) {
                        \File::delete(storage_path('app/public/images/setting/'. $this->attributes['website_logo']));
                    }
                }
                $website_logo = uploadImg($value,'setting');
                $this->attributes['website_logo'] = $website_logo;
            }
        }
    }


    public function getImageAttribute()
    {
        $website_logo = isset($this->attributes['website_logo']) && $this->attributes['website_logo'] ? asset('storage/images/setting/'.$this->attributes['website_logo']) : null;
        return $website_logo;
    }
}
