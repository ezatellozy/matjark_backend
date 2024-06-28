<?php

namespace App\Http\Controllers\Api\Provider\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Product\ProductRequest;
use App\Http\Requests\Api\Provider\Product\UpdateProductQuantityRequest;
use App\Http\Resources\Api\Provider\Product\{ProductResource, ShowProductResource, SimpleProductResource};
use App\Models\{Category, Color, Product, ProductDetails, ProductMedia, Size};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->guard('api')->user();

        $categories = null ;
        $category = null;
        if($request->category_id != null){
            $category = Category::where(['is_active' => true, 'id' => $request->category_id])->first();
            $categories  =         $category  != null?  thirdLavels($category) : null;
        }

        $products = Product::where('added_by_id',$user->id)->when($request->keyword, function ($query) use($request) {
            $query->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('desc', '%'.$request->keyword.'%');
        })
        
        ->when($request->code , function($q) use($request){
            $q->where('code', $request->code);
        })->when($categories != null  ||  $category != null , function($q) use($categories, $category){
           $q->whereHas('categoryProducts', function ($q) use ($categories, $category) {
            if ($categories != null) {
                $q->whereIn('category_id', $categories->pluck('id')->toArray());
            } else {
                $q->where('category_id', $category->id);
            }
        });
        })
        ->whereHas('productDetails', function ($query) use($request) {
            $query->when($request->from_price, function ($query) use ($request) {
                $query->where('price', '>=', $request->from_price);
            })->when($request->to_price, function ($query) use ($request) {
                $query->where('price', '<=', $request->to_price);
            })->when($request->from_quantity, function ($query) use ($request) {
                $query->where('quantity', '>=', $request->from_quantity);
            })->when($request->to_quantity, function ($query) use ($request) {
                $query->where('quantity', '<=', $request->to_quantity);
            })->when($request->quantity, function ($query) use ($request) {
                $query->where('quantity', $request->quantity);
            })->when($request->color_id, function ($query) use($request) {
                $query->where('color_id', $request->color_id);
            })->when($request->size_id, function ($query) use($request) {
                $query->where('size_id', $request->size_id);
            })->when($request->features, function ($query) use ($request) {
                foreach ($request->features as $feature)
                {
                    $query->whereJsonContains('features', ['feature_id' => isset($feature['feature_id']) ? $feature['feature_id'] : null, 'value_id' => isset($feature['value_id']) ? $feature['value_id'] : null]);
                }
            });
        })->latest()->paginate();

        return SimpleProductResource::collection($products)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {


        DB::beginTransaction();
        try
        {
            $user = auth()->guard('api')->user();
            $product = Product::create(array_except($request->validated(), ['product_details']) + ['main_category_id' => $request->main_category->id,'added_by_id' => $user->id ]);
            $product->categories()->attach($request->category_ids);

            foreach ($request->product_details as $product_detail)
            {
                $color_data = isset($product_detail['color_id']) ? Color::find($product_detail['color_id'])->toJson() : null;
                $size_data  = isset($product_detail['size_id']) ? Size::find($product_detail['size_id'])->toJson() : null;

                $product_details = $product->productDetails()->create(array_except($product_detail, ['media', 'features']) + ['color_data' => $color_data, 'size_data' => $size_data, 'features' => isset($product_detail['features']) ? $product_detail['features'] : null]);

                foreach ($product_detail['media'] as $media)
                {
                    $image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
                    $product->media()->create(['product_details_id' => $product_details->id ,'color_id' => null, 'media' => $image, 'media_type' => 'image']);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.create.success')]);
        }
        catch (Exception $e)
        {
            //dd($e->getMessage());
            DB::rollBack();
            //dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.create.fail')], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->guard('api')->user();
        $product = Product::where('added_by_id',$user->id)->where('id',$id)->firstOrFail();
        return (new ProductResource($product))->additional(['status' => 'success', 'message' => '']);
    }

    public function showProduct($id)
    {
        $user = auth()->guard('api')->user();
        $product = Product::where('added_by_id',$user->id)->where('id',$id)->firstOrFail();
        return (new ShowProductResource($product))->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        DB::beginTransaction();
        try
        {
            $product->update(array_except($request->validated(), ['product_details']));

            if ($request->category_ids)
            {
                $product->categories()->sync($request->category_ids);
            }

            foreach ($request->product_details as $product_detail)
            {
                $color_data = isset($product_detail['color_id']) ? Color::find($product_detail['color_id']) : null;
                $size_data  = isset($product_detail['size_id']) ? Size::find($product_detail['size_id']) : null;
                $product_details = isset($product_detail['product_detail_id']) ? ProductDetails::find($product_detail['product_detail_id']) : null;

                $product_details = ProductDetails::updateOrCreate(['id' => isset($product_detail['product_detail_id']) ? $product_detail['product_detail_id'] : null], array_except($product_detail, ['media']) + ['product_id' => $product->id, 'color_data' => $color_data, 'size_data' => $size_data, 'features' => isset($product_detail['features']) ? $product_detail['features'] : null]);

                if (isset($product_detail['media']))
                {
                    foreach ($product_detail['media'] as $media)
                    {
                        if (isset($media['image_id']))
                        {
                            $image = ProductMedia::find($media['image_id']);
                            File::delete(storage_path('app/public/images/products/'.$image->product_id.'/'.$image->product_details_id.'/'.$image->media));
                            $new_image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
                            $image ->update(['media' => $new_image]);
                        }
                        else
                        {
                            $image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
                            $product->media()->create(['product_details_id' => $product_details->id ,'color_id' => null, 'media' => $image, 'media_type' => 'image']);
                        }
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.update.success')]);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.update.fail')], 422);
        }
    }

    public function deleteProductDetailsImage($product_id, $image_id)
    {
        $image = ProductMedia::where('product_id', $product_id)->findOrFail($image_id);

        if (ProductMedia::where('product_details_id', $image->product_details_id)->count() > 1)
        {
            if ($image->delete())
            {
                File::delete(storage_path('app/public/images/products/'.$image->product_id.'/'.$image->product_details_id.'/'.$image->media));
                return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.success')]);
            }

            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
        }
        else
        {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.error.details_must_contain_one_image')], 422);
        }
    }

    public function deleteProductDetail($product_id, $product_detail_id)
    {
        $product = Product::findOrFail($product_id);
        $product_detail = ProductDetails::where('product_id', $product_id)->findOrFail($product_detail_id);

        if (count($product->productDetails) > 1)
        {
            if ($product_detail->delete())
            {
                File::deleteDirectory(storage_path('app/public/images/products/'.$product_id.'/'.$product_detail_id));
                return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.success')]);
            }
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
        }
        else
        {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.error.product_must_contain_one_details')], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        DB::beginTransaction();
        try
        {
            // $product->media()->delete();
            // File::deleteDirectory(storage_path('app/public/images/products/'.$product->id));
            $product->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.success')]);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
        }
    }

    public function productsWithoutPaginate(Request $request)
    {
        $products = Product::when($request->name, function ($query) use($request) {
            $query->whereTranslationLike('name', '%'.$request->name.'%');
        })
        ->when($request->code , function($q) use($request){
            $q->where('code', $request->code);
        })->get();

        return SimpleProductResource::collection($products)->additional(['status' => 'success', 'message' => '']);
    }

    public function updateQuantity(UpdateProductQuantityRequest $request, $product_id, $product_detail_id)
    {
        $product_detail = ProductDetails::where('product_id', $product_id)->findOrFail($product_detail_id);
        $product_detail->increment('quantity', $request->quantity);
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.update.success')]);
    }
}
