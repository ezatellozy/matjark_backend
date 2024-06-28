<?php

namespace App\Http\Controllers\Api\Dashboard\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Category\CategoryRequest;
use App\Http\Resources\Api\Dashboard\Category\CategoryDetailsResource;
use App\Http\Resources\Api\Dashboard\Category\CategoryFeatureResource;
use App\Http\Resources\Api\Dashboard\Category\CategoryResource;
use App\Http\Resources\Api\Dashboard\Category\CategorySimpleResource;
use App\Http\Resources\Api\Dashboard\Category\TreetCategoryResource;
use App\Http\Resources\Api\Dashboard\Color\ColorResource;
use App\Http\Resources\Api\Dashboard\Feature\FeatureResource;
use App\Http\Resources\Api\Dashboard\Size\SizeResource;
use App\Models\Category;
use App\Models\Color;
use App\Models\Feature;
use App\Models\Size;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::when($request->category_id == null, function ($query) {
            $query->where('position', 'main');
        })->when($request->category_id, function ($query) use($request) {
            $query->where('parent_id', $request->category_id);
        })->when($request->position, function ($query) use($request) {
            $query->where('position', $request->position);
        })
        
        ->when($request->keyword, function ($query) use($request) {
            $query->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('desc', '%'.$request->keyword.'%');
        })
        ->latest()->paginate();

        return CategoryResource::collection($categories)->additional(['status' => 'success', 'message' => null]);
    }

    public function CategoriesWithOutPagination(Request $request)
    {
        $categories = Category::when($request->category_id == null, function ($query) {
            $query->where('position', 'main');
        })->when($request->category_id, function ($query) use($request) {
            $query->where('parent_id', $request->category_id);
        })->when($request->position, function ($query) use($request) {
            $query->where('position', $request->position);
        })
        
        ->when($request->keyword, function ($query) use($request) {
            $query->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('desc', '%'.$request->keyword.'%');
        })
        ->latest()->get();

        return CategoryResource::collection($categories)->additional(['status' => 'success', 'message' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        // $slug_ar = str_replace(" ","_",$request->ar['name']);
        // $slug_en = str_replace(" ","_",$request->en['name']);

        $parent = $request->parent_id ? Category::find($request->parent_id) : null;

        // if($parent){
        //     $slug_ar = str_replace(" ","_",$request->ar['name']) . '-' . $parent->translate('ar')->slug;
        //     $slug_en = str_replace(" ","_",$request->en['name']) . '-' . $parent->translate('en')->slug;
        // }

        $category = Category::create($request->validated() + ['added_by_id' => auth('api')->id(), 'position' => $parent ? ($parent->position == 'main' ? 'first_sub' : 'second_sub') : 'main' , 'parent_id'=>$request->parent_id]);
        
        // $category_data = $request->safe()->only(['ar.name', 'ar.slug', 'en.name', 'en.slug', 'ar.desc', 'en.desc']);
        // $category = Category::create($category_data + ['added_by_id' => auth('api')->id(), 'position' => $parent ? ($parent->position == 'main' ? 'first_sub' : 'second_sub') : 'main' , 'parent_id'=>$request->parent_id]);
        
        // $category->update([
        //     'en' => ['slug' => $slug_en],
        //     'ar' => ['slug' => $slug_ar]
        // ]);

        $meta_data = $category->metas()->create($request->safe()->except(['ar.name', 'ar.slug', 'en.name', 'en.slug', 'ar.desc', 'en.desc']));
        return CategoryResource::make($category)->additional(['status' => 'success', 'message' => trans('dashboard.create.category')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);
        return (new CategoryDetailsResource($category))->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $parent = isset($request->parent_id) ? Category::find($request->parent_id) : null;

        $category = Category::findOrFail($id);

        // if($category->translate('ar')->name != $request->ar['name']){

        // $slug_ar = str_replace(" ","_",$request->ar['name']);

        // $category->update([
        //     'ar' => ['slug' => $slug_ar]
        // ]);


        // }
        // if($category->translate('en')->name != $request->en['name']){

        // $slug_en = str_replace(" ","_",$request->en['name']);

        // $category->update([
        //     'en' => ['slug' => $slug_en],
        // ]);

        // }
            
        // if($parent){
        //     $slug_ar = str_replace(" ","_",$request->ar['name']) . '-' . $parent->translate('ar')->slug;

        //     $category->update([
        //         'ar' => ['slug' => $slug_ar]
        //     ]);

        //     $slug_en = str_replace(" ","_",$request->en['name']) . '-' . $parent->translate('en')->slug;

        //     $category->update([
        //     'en' => ['slug' => $slug_en],
        //     ]);
        // }
        
        // $category_data = $request->safe()->only(['ar.name', 'ar.slug', 'en.name', 'en.slug', 'ar.desc', 'en.desc']);
        // $category->update($category_data + ['position' => $parent ? ($parent->position == 'main' ? 'first_sub' : 'second_sub') : 'main']);

        $category->update($request->validated() + ['position' => $parent ? ($parent->position == 'main' ? 'first_sub' : 'second_sub') : 'main']);

        $meta_data = $request->safe()->except(['ar.name', 'ar.slug', 'en.name', 'en.slug', 'ar.desc', 'en.desc']);
        
        // return dd($category->media);
        // foreach($category->media as $media){
        //     return $media;

        $alt_en = $request->image_alt_en?? null;
        $alt_ar = $request->image_alt_ar?? null;
        $category->media->update(['alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
        
        // }
            
        if($category->metas()->count()){
            $category->metas->update($meta_data);
        }else{
            $meta_data = $category->metas()->create($request->safe()->only(['ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description', 'meta_canonical_tag']));
        }
        // $meta_data = $category->metas()->update($request->safe()->except(['ar.name', 'ar.slug', 'en.name', 'en.slug']));
        return CategoryResource::make($category)->additional(['status' => 'success', 'message' => trans('dashboard.update.category')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if($category->metas){
            $category->metas->delete();
        }
        if ($category->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.category')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }

    public function getAllParentsCategory()
    {
        $paretns = Category::whereIn('position', ['main', 'first_sub'])->latest()->get();

        return CategorySimpleResource::collection($paretns)->additional(['status' => 'success', 'message' => '']);
    }

    public function getAllChildrenCategory()
    {
        $children = Category::whereNotIn('position', ['main', 'first_sub'])->latest()->get();

        return CategorySimpleResource::collection($children)->additional(['status' => 'success', 'message' => '']);
    }

    public function getCategoryFeatures($id)
    {
        $category = Category::findOrFail($id);

        return (new CategoryFeatureResource($category))->additional(['status' => 'success', 'message' => '']);
    }
    
    

    public function getCategoriesFeatures(Request $request)
    {
        $categories = Category::whereIn('id', $request->category_ids)->get();

        $colors     = Color::get();
        $features   = [];
        $sizes      = [];
             $unique_data  = [];
        foreach($categories as $category){
            $features ? $features->merge(getCategoryFeatures($category)) : $features = getCategoryFeatures($category);
            $sizes ? $sizes->merge(getCategorySizes($category)) : $sizes = getCategorySizes($category);
        }

        if( count($sizes) > 0){
            $collection = collect($sizes);

            $unique_data = $collection->unique('id')->all(); 
            // dd($unique_data);

        }
        
        if($features != null && $features->count() > 0) {
            $features_arr = $features->pluck('id')->toArray();
            $features_arr = array_unique($features_arr);
            $features = Feature::whereIn('id',$features_arr)->get();
        }

        if($sizes != null && $sizes->count() > 0) {
            $sizes_arr = $sizes->pluck('id')->toArray();
            $sizes_arr = array_unique($sizes_arr);
            $sizes = Size::whereIn('id',$sizes_arr)->get();
        }

        return response()->json([
            "status"  => "success",
            "data"    => [
                "colors"   => ColorResource::collection($colors),
                "sizes"    =>count($unique_data) > 0 ?  SizeResource::collection($unique_data):[],
                "features" => FeatureResource::collection($features)
            ],
            "message" => "",
        ]);
    }

    public function getAllCategories()
    {
        $categories = Category::get();

        return CategorySimpleResource::collection($categories)->additional(['status' =>'success', 'message' => '']);
    }

    public function getAllMainCategories()
    {
        $categories = Category::where('position', 'main')->get();

        return CategorySimpleResource::collection($categories)->additional(['status' =>'success', 'message' => '']);
    }

    public function getAllThirdLevelCategories($category)
    {
        $category = Category::where('position', 'main')->findOrFail($category);
        $third_level_categories = allThirdLavels($category);

        return CategorySimpleResource::collection($third_level_categories ? $third_level_categories:[])->additional(['status' =>'success', 'message' => '']);
    }

    public function categoryTrees()
    {
        $categories = Category::where(['is_active' => true, 'position' => 'main', 'parent_id' => null])->get();
        return TreetCategoryResource::collection($categories)->additional(['status' => 'success', 'message' => '']);
    }

    public function getLastCategory($category)
    {
        $category = Category::findOrFail($category);
        $last_level_categories = lastLevel($category);
        
        return $last_level_categories;
    }


    public function LastCategories(){
        $lastCategories = Category::where('is_active' ,true)->whereDoesntHave('children')->latest()->get();
        return CategorySimpleResource::collection($lastCategories)->additional(['status' => 'success', 'message' => '']);
    }
     
}
