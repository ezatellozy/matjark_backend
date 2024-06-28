<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CommonQuestions;

class Product extends Model implements TranslatableContract
{
    use HasFactory, Translatable;
    
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public $translatedAttributes = ['name', 'slug', 'desc'];

    protected static function boot()
    {
        parent::boot();
        Product::observe(ProductObserver::class);
    }

    public function main_category()
    {
        return $this->belongsTo(Category::class,'main_category_id');
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function productDetails()
    {
        return $this->hasMany(ProductDetails::class, 'product_id');
    }
    // public function cartProduct()
    // {
    //     return $this->belongsTo(CartProduct::class, 'product_id');
    // }
    // public function ProductDetailInCart(Request $request)
    // {
    //     return $this->belongsTo(ProductDetails::class, 'product_detail_id')->cartProduct()->cart()->where('user_id', auth('api')->id())->orWhere('guest_token', $request->guest_token);
    // }
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function metas()
    {
        return $this->morphOne(Meta::class, 'metable');
    }
    
    public function commonQuestions(){
        return $this->hasMany(CommonQuestion::class);
    }


    public function flashSaleProducts()
    {
        return $this->hasMany(FlashSaleProduct::class, 'product_id');
    }

    public function categoryProducts()
    {
        return $this->hasMany(CategoryProduct::class, 'product_id');
    }

    public function getSizeGuideAttribute()
    {
        $media = $this->media()->where('option', 'size_guide')->first();
        $locale = app()->getLocale()?? 'ar';

        $media_path = null;

        if ($media  != null) {
            $media_path  = 'storage/images/products/'.$this->id .'/'. $media->media;
            $alt = $media->{'alt_'.$locale};
        }
        // dd($this->media()->where('option', 'size_guide')->first());
        return [
            'media' => $media_path  != null ?  asset($media_path) : null,
            'alt' => $alt??null,
            'image_alt_ar' => @$media->alt_ar,
            'image_alt_en' => @$media->alt_en
            ];
        // return $media  != null ?  asset($media) : null;
    }



}
