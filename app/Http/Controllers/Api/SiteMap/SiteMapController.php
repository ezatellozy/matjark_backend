<?php

namespace App\Http\Controllers\Api\SiteMap;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SiteMap\ProductResource;
use App\Models\Product;
use App\Http\Resources\Api\SiteMap\CategoryResource;
use App\Models\Category;
use App\Models\About;
use App\Models\Privacy;
use App\Models\Setting;

class SiteMapController extends Controller
{
    public function mainSiteMap()
    {
        $products = Product::all();

        $mapped_products = $this->products($products);
        $mapped_categories = $this->categories();
        $mapped_about = $this->about();
        $mapped_privacy = $this->privacy();
        // $mapped_return_policy = $this->returnPolicy();

        // $return = array_merge($mapped_products, $mapped_categories, $mapped_about, $mapped_privacy, $mapped_return_policy);
        $return = array_merge($mapped_products, $mapped_categories, $mapped_about, $mapped_privacy);

        return response()->json([
            'data' => $return,
            'status' => 'success',
            'message' => ''
        ]);
    }

    public function products($products){

        $mapped_products = [];
        foreach($products as $product){
            $translations = $product->translations;
            foreach($translations as $translation){
                $locale = $translation->locale;
                $url = ($locale == 'ar')? 'products/'.$product->translate('ar')->slug: 'en/products/'.$product->translate('en')->slug;
                $mapped_products [] = [
                    'loc' => $url,
                    'lastmod' => $product->updated_at,
                    'changefreq' => 'daily'
                    ];
            }
        }

        return $mapped_products;
    }

    /**
     * @return void
     * Handle products site map
     */
    public function productsSiteMap()
    {
        $products = Product::all();
        $mapped_products = $this->products($products);
         return response()->json([
            'data' => $mapped_products,
            'status' => 'success',
            'message' => ''
            ]);
    }

    public function categories(){
        $categories = Category::all();
        $mapped_categories = [];
        foreach($categories as $category){
            $translations = $category->translations;
            foreach($translations as $translation){
                $locale = $translation->locale;
                $url = ($locale == 'ar')? 'categories/'.$category->translate('ar')->slug: 'en/categories/'.$category->translate('en')->slug;
                $mapped_categories [] = [
                    'loc' => $url,
                    'lastmod' => $category->updated_at,
                    'changefreq' => 'daily'
                    ];
            }
        }
        return $mapped_categories;
    }

    public function categoriesSiteMap(){
        $mapped_categories = $this->categories();
         return response()->json([
            'data' => $mapped_categories,
            'status' => 'success',
            'message' => ''
            ]);
    }

    public function about(){
        $about = About::first();
        $translations = $about->translations;
            foreach($translations as $translation){
                $locale = $translation->locale;

                // $url = ($locale == 'ar')? 'about/'.$about->translate('ar')->slug: 'en/about/'.$about->translate('en')->slug;

                $url = ($locale == 'ar')? 'about-us': 'en/about-us';

                $mapped_about [] = [
                    'loc' => $url,
                    'lastmod' => $about->updated_at,
                    'changefreq' => 'daily'
                    ];
            }
        return $mapped_about;
    }

    public function aboutSiteMap(){
        $mapped_about = $this->about();
        return response()->json([
            'data' => $mapped_about,
            'status' => 'success',
            'message' => ''
            ]);
    }
    public function privacy(){
        $privacy = Privacy::first();
        $translations = $privacy->translations;
        foreach($translations as $translation){
            $locale = $translation->locale;

            //$url = ($locale == 'ar')? 'privacy_policy/'.$privacy->translate('ar')->slug: 'en/privacy_policy/'.$privacy->translate('en')->slug;
            $url = ($locale == 'ar')? 'return-policy': 'en/return-policy';

            $mapped_privacy [] = [
                'loc' => $url,
                'lastmod' => $privacy->updated_at,
                'changefreq' => 'daily'
                ];
        }
        return $mapped_privacy;
    }
    public function privacySiteMap(){
        $mapped_privacy = $this->privacy();
        return response()->json([
            'data' => $mapped_privacy,
            'status' => 'success',
            'message' => ''
            ]);
    }

    public function returnPolicy(){
        $return_policy_ar = Setting::where('key', 'return_policy_ar')->first();
        $return_policy_en = Setting::where('key', 'return_policy_en')->first();
        $mapped_return_policy = [
            [
                'loc' => 'policy',
                'lastmod' => $return_policy_ar->updated_at,
                'changefreq' => 'daily'
            ],
            [
                'loc' => 'en/policy',
                'lastmod' => $return_policy_en->updated_at,
                'changefreq' => 'daily'
            ]
        ];
        return $mapped_return_policy;
    }

    public function returnPolicySiteMap(){
        $mapped_return_policy = $this->returnPolicy();
        return response()->json([
            'data' => $mapped_return_policy,
            'status' => 'success',
            'message' => ''
            ]);
    }

    public function categoryProductsSiteMap($slug){
        $category = Category::whereTranslationLike('slug', $slug)->first();
        $products = Product::where('main_category_id', $category->id)->get();
        $mapped_products = $this->products($products);
        return response()->json([
            'data' => $mapped_products,
            'status' => 'success',
            'message' => ''
            ]);
    }
}
