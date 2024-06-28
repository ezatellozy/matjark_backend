<?php

namespace App\Models;

use App\Observers\CategoryObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;


use function PHPUnit\Framework\returnSelf;

class Category extends Model implements TranslatableContract
{
    use Translatable, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['name', 'slug', 'desc'];

    protected static function boot()
    {
        parent::boot();
        Category::observe(CategoryObserver::class) ;
    }

    // image
    public function getImageAttribute()
    {
        $image = $this->media()->exists() ? 'storage/images/categories/'.$this->media()->first()->media : 'dashboardAssets/images/banner/banner-2.jpg';

        return asset($image);
    }
    
    public function getCropImageAttribute()
    {
        $image = $this->media()->exists() ? 'storage/images/categories/crop/' . $this->media()->first()->media : 'dashboardAssets/images/banner/banner-2.jpg';

        return asset($image);
    }

    public function media()
    {
    	return $this->morphOne(AppMedia::class,'app_mediaable');
    }
    
    public function metas()
    {
        return $this->morphOne(Meta::class, 'metable');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class);
    }
      public function mainCategoryProducts()
    {
        return $this->hasMany(Product::class, 'main_category_id');
    }
    public function sliders()
    {
        return $this->hasMany(Slider::class,'category_id');
    }

    public function translation_row()
    {
        return $this->belongsTo(CategoryTranslation::class);
    }
    
    public function products(){
        return $this->hasMany(Product::class, 'main_category_id');
    }

}
