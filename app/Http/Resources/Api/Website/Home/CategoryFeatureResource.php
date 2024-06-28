<?php

namespace App\Http\Resources\Api\Website\Home;

use App\Http\Resources\Api\Website\General\ColorResource;
use App\Http\Resources\Api\Website\General\FeatureResource;
use App\Http\Resources\Api\Website\General\SizeResource;
use App\Models\CategoryProduct;
use App\Models\Color;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Size;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryFeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->position == 'main') {
            // $products_arr1 = Product::where('is_active', true)->where('main_category_id', $this->id)->pluck('id')->toArray();
            
            // $categories_arr = Category::where('parent_id',$this->id)->pluck('id')->toArray();
            
            // $category_products_arr = ! empty($categories_arr) ? CategoryProduct::whereIn('category_id',$categories_arr)->pluck('product_id')->toArray() : [];
            
            // $products_arr2 = ! empty($category_products_arr) ? Product::where('is_active', true)->whereIn('id', $category_products_arr)->pluck('id')->toArray() : [];
            
            // $products_arr = array_merge($products_arr1,$products_arr2);
            
            $category = Category::where(['is_active' => true, 'id' => $this->id])->firstOrFail();

            $categories  =  lastLevel($category);
            
            $products_arr = Product::where('is_active', true)->whereHas('categoryProducts', function ($q) use ($categories, $category) {
                if (count($categories) >0) {
                    $q->whereIn('category_id', $categories->pluck('id')->toArray());
                } else {
                    $q->where('category_id', $category->id);
                }
            })->pluck('id')->toArray();
            

        } else {
            $categories_count = Category::where('parent_id',$this->id)->count();
            
            if($categories_count > 0) {
                
                $categories_arr = Category::where('parent_id',$this->id)->pluck('id')->toArray();
                
                $category_products_arr = ! empty($categories_arr) ? CategoryProduct::whereIn('category_id',$categories_arr)->pluck('product_id')->toArray() : [];

                $products_arr = ! empty($category_products_arr) ? Product::where('is_active', true)->whereIn('id', $category_products_arr)->pluck('id')->toArray() : [];
                
            } else {
                $category_products_arr = CategoryProduct::where('category_id',$this->id)->pluck('product_id')->toArray();
                                
                $products_arr = ! empty($category_products_arr) ? Product::where('is_active', true)->whereIn('id', $category_products_arr)->pluck('id')->toArray() : [];
            }
        }
        
        $colors_arr = ProductDetails::whereIn('product_id',$products_arr)->pluck('color_id')->toArray();
        $sizes_arr = ProductDetails::whereIn('product_id',$products_arr)->pluck('size_id')->toArray();
        $features_arr = ProductDetails::whereIn('product_id',$products_arr)->pluck('features')->toArray();

        $custom_features_arr = [];

        if(! empty($features_arr)) {

            foreach($features_arr as $custom_feature_arr) {
                $new_arr = array_column($custom_feature_arr, 'feature_id');
                $custom_features_arr = array_merge($custom_features_arr,$new_arr);
            }

        }

        if(! empty($custom_features_arr)) { 
            $custom_features_arr = array_unique($custom_features_arr);
        }

        // $categories_arr = thirdLavels($this) != null ? thirdLavels($this)->pluck('product_id')->toArray() : [];
        
        // $category_products_arr = ! empty($categories_arr) ? CategoryProduct::whereIn('category_id',$categories_arr)->pluck('product_id')->toArray() : [];

        // $products_arr = ! empty($category_products_arr) ? Product::where('is_active', true)->whereIn('id', $category_products_arr)->pluck('id')->toArray() : [];


        // $max_product_price = ProductDetails::whereHas('product', function ($query) {
        //     $query->whereHas('categories', function ($query) {
        //         $query->where('category_id', $this->id);
        //     });
        // })->max('price');
        
        
        // $categories_arr =  thirdLavels($this) != null && thirdLavels($this)->count() > 0 ? thirdLavels($this)->pluck('id')->toArray() : [];
        
        // //dd($categories_arr);
        
        // $category_products_arr = ! empty($categories_arr) ? CategoryProduct::whereIn('category_id',$categories_arr)->pluck('product_id')->toArray() : [];

        // $products_arr = ! empty($category_products_arr) ? Product::where('is_active', true)->whereIn('id', $category_products_arr)->pluck('id')->toArray() : [];
        
        $colors = Color::whereIn('id',$colors_arr)->get();

        $sizes_arr = array_unique($sizes_arr);
        $sizes = Size::whereIn('id',$sizes_arr)->get();

        $features = Feature::whereIn('id',$custom_features_arr)->get();
        
        $categories = lastLevel($this->resource);

        $max_product_price = ProductDetails::whereHas('product', function ($query) use ($categories) {
            $query->whereHas('categories', function ($query) use ($categories) {
                if (count($categories) >0) {
                    $query->whereIn('category_id', $categories->pluck('id')->toArray());
                } else {
                    $query->where('category_id', $this->id);
                }
            });
        })->max('price');

        //$features = getCategoryFeatures($this);
        
        //$sizes = getCategorySizes($this->resource);
        
        return [
            'colors'    => ColorResource::collection($colors),
            // 'sizes'     => SizeResource::collection($this->sizes),
            // 'features'  => FeatureResource::collection($this->features),
            'sizes'     => $sizes ?  SizeResource::collection($sizes) : [],
            'features' => $features ? FeatureResource::collection($features) : [],
            'max_price' => (float) $max_product_price,
        ];
    }
}

