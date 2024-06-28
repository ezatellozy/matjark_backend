<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = ['created_at','updated_at','deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($data) {

            if (request()->hasFile('logo')) {
                if ($data->media()->whereNull('option')->exists()) {
                    $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Company','app_mediaable_id' => $data->id ,'media_type' => 'image'])->whereNull('option')->first();
                    $image->delete();
                    if (file_exists(storage_path('app/public/images/company/'.$image->media))){
                        \File::delete(storage_path('app/public/images/company/'.$image->media));
                        $image->delete();
                    }
                }
                $image = uploadImg(request()->logo, 'company');
                $data->media()->create(['media' => $image,'media_type' => 'image']);
            }
        });

        static::deleted(function ($data) {
            if ($data->media()->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Company','app_mediaable_id' => $data->id ,'media_type' => 'image'])->first();
                if (file_exists(storage_path('app/public/images/company/'.$image->media))){
                    \File::delete(storage_path('app/public/images/company/'.$image->media));
                }
                $image->delete();
            }
        });
    }

    public function getLogoUrlAttribute()
    {
        $logo = $this->media()->whereNull('option')->first();
        $image = $logo ? 'storage/images/company/'. $logo->media : 'dashboardAssets/images/logo/logo.png';
        return asset($image);
    }

    // Relations
    // ========================= Image ===================
    public function media()
    {
    	return $this->morphOne(AppMedia::class,'app_mediaable');
    }

    

}
