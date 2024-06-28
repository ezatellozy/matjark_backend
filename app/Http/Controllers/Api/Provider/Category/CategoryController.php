<?php

namespace App\Http\Controllers\Api\Provider\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Category\CategoryRequest;
use App\Http\Resources\Api\Provider\Category\CategoryDetailsResource;
use App\Http\Resources\Api\Provider\Category\CategoryFeatureResource;
use App\Http\Resources\Api\Provider\Category\CategoryResource;
use App\Http\Resources\Api\Provider\Category\CategorySimpleResource;
use App\Http\Resources\Api\Provider\Category\TreetCategoryResource;
use App\Http\Resources\Api\Provider\Color\ColorResource;
use App\Http\Resources\Api\Provider\Feature\FeatureResource;
use App\Http\Resources\Api\Provider\Size\SizeResource;
use App\Models\Category;
use App\Models\Color;
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $parent = isset($request->parent_id) ? Category::find($request->parent_id) : null;
        $category = Category::create($request->validated() + ['added_by_id' => auth('api')->id(), 'position' => $parent ? ($parent->position == 'main' ? 'first_sub' : 'second_sub') : 'main']);

        return CategoryResource::make($category)->additional(['status' => 'success', 'message' => trans('provider.create.category')]);
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

        $category->update($request->validated() + ['position' => $parent ? ($parent->position == 'main' ? 'first_sub' : 'second_sub') : 'main']);

        return CategoryResource::make($category)->additional(['status' => 'success', 'message' => trans('provider.update.category')]);
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

        if ($category->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.category')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
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
        $categories = Category::all();

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
