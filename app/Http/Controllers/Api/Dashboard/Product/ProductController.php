<?php

namespace App\Http\Controllers\Api\Dashboard\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Product\ProductRequest;
use App\Http\Requests\Api\Dashboard\Product\UpdateProductQuantityRequest;
use App\Http\Resources\Api\Dashboard\Product\{Product2Resource, ProductItemResource, ProductOrdersResource, ProductResource, ProductSummaryResource, ReturnedOrderProductsResource, ShowProductResource, SimpleProductResource};
use App\Models\{Cart, CartProduct, Category, Color, FavouriteProduct, FlashSaleProduct, Order, OrderProduct, Product, ProductDetails, ProductMedia, ReturnOrder, ReturnOrderProduct, Size, User};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image as Image;

class ProductController extends Controller
{


    public function getActiveProducts(Request $request)
    {
        $products = Product::where('is_active', true)->whereHas('productDetails',function($productDetails) {
            $productDetails->where('quantity','>',0);
        })->latest()->get();

        return ProductItemResource::collection($products)->additional(['status' => 'success', 'message' => '']);
    }

    public function toggleActive($id)
    {
        $product = Product::where('id',$id)->firstOrFail();
        $product->update(['is_active' => !$product->is_active]);
        return ProductResource::make($product)->additional(['status' => 'success', 'message' => '']);
    }


    public function product_statistics($id)
    {
        $product = Product::findOrFail($id);

        $product_detailsArr = ProductDetails::where('product_id',$id)->pluck('id')->toArray();

        $data['statistics'] = [

            "nums_order" =>  Order::whereHas('orderProducts',function($orderProducts) use($id) {
                $orderProducts->where('product_id',$id);
            })->count(),

            "nums_cart" =>  Cart::whereHas('cartProducts',function($cartProducts) use($id) {
                $cartProducts->where('product_id',$id);
            })->count(),

            "nums_favourites" =>  User::where('user_type', 'client')->whereHas('favouriteProducts',function($favouriteProducts) use($id,$product_detailsArr) {
                // $favouriteProducts->where('product_id',$id);
                $favouriteProducts->whereIn('product_detail_id',$product_detailsArr);
            })->count(),

            "nums_returns"    => ReturnOrder::whereHas('returnOrderProducts',function($returnOrderProducts) use ($product_detailsArr) {
                $returnOrderProducts->whereIn('product_detail_id',$product_detailsArr);
            })->count(),

            "nums_clients"    => 0,
        ];

        return response()->json(['status' => 'success', 'data' => $data, 'message' => '']);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = null ;
        $category = null;

        if($request->category_id != null){
            $category = Category::where(['is_active' => true, 'id' => $request->category_id])->first();
            $categories  =         $category  != null?  thirdLavels($category) : null;
        }

        $products = Product::when($request->keyword, function ($query) use($request) {
            $query->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('desc', '%'.$request->keyword.'%');
        })

        ->when($request->code , function($q) use($request){

            $q->where('code', $request->code);

        })->when($request->status, function($query) use($request){

            if($request->status == 'active') {
                $query->where('is_active', 1);
            } elseif($request->status == 'inactive') {
                $query->where('is_active', 0);
            }

        })->when($request->in_stock, function($query) use($request){

            if($request->in_stock == 'yes') {
                $query->whereHas('productDetails',function($productDetails) {
                    $productDetails->where('quantity','>',0);
                });
            } elseif($request->in_stock == 'no') {
                $query->whereHas('productDetails',function($productDetails) {
                    $productDetails->where('quantity','=',0);
                });
            }

        })
        ->when($request->discount_type, function($query) use($request){

            if($request->discount_type == 'offer') {
                $query->whereHas('productDetails',function($productDetails) {
                    $productDetails->whereHas('offerProducts');
                });
            } elseif($request->discount_type == 'flash_sale') {
                $query->whereHas('productDetails',function($productDetails) {
                    $productDetails->whereHas('flashSalesProduct');
                });
            }

        })

        ->when($categories != null  ||  $category != null , function($q) use($categories, $category){
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
            })->when($request->color, function ($query) use($request) {
                $query->where('color_id', $request->color);
            })->when($request->size, function ($query) use($request) {
                $query->where('size_id', $request->size);
            })
            // ->when($request->size_id, function ($query) use($request) {
            //     $query->where('size_id', $request->size_id);
            // })
            ->when($request->features, function ($query) use ($request) {
                foreach ($request->features as $feature)
                {
                    $query->whereJsonContains('features', ['feature_id' => isset($feature['feature_id']) ? $feature['feature_id'] : null, 'value_id' => isset($feature['value_id']) ? $feature['value_id'] : null]);
                }
            });

        })->when($request->from_time && $request->to_time, function ($query) use ($request) {

            $query->whereBetween('created_at', [$request->from_time, $request->to_time]);

        })->when($request->from_time, function ($query) use ($request) {

            $query->whereDate('created_at','>=',$request->from_time);

        })->when($request->to_time, function ($query) use ($request) {

            $query->whereDate('created_at','<=',$request->to_time);

        })->latest()->paginate(16);

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
            $slug_ar = str_replace(" ","_",$request->ar['name']);
            $slug_en = str_replace(" ","_",$request->en['name']);

            $product = Product::create(
                    array_except( $request->validated(), ['product_details', 'ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description'])
                    +
                    ['main_category_id' => $request->main_category->id]
            );

            $product->update([
                'en' => ['slug' => $slug_en],
                'ar' => ['slug' => $slug_ar]
            ]);

            $product->categories()->attach($request->category_ids);

            $meta_data = $product->metas()->create($request->safe()->only(['ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description', 'meta_canonical_tag']));

            foreach ($request->product_details as $product_detail)
            {
                $color_id = isset($product_detail['color_id']) ? $product_detail['color_id'] : null;
                $color_data = isset($product_detail['color_id']) ? Color::find($product_detail['color_id'])->toJson() : null;

                foreach ($product_detail['sizes'] as $sizeArr) {

                    $size_data  = isset($sizeArr['size_id']) ? Size::find($sizeArr['size_id'])->toJson() : null;

                    $size_id  = isset($sizeArr['size_id']) ? $sizeArr['size_id'] : null;

                    $price  = isset($sizeArr['price']) ? $sizeArr['price'] : 0;
                    $quantity  = isset($sizeArr['quantity']) ? $sizeArr['quantity'] : 0;

                    $product_detail_arr = array_except($product_detail, ['media', 'features']);
                    $product_attributes_arr = ['color_data' => $color_data, 'size_data' => $size_data, 'features' => isset($product_detail['features']) ? $product_detail['features'] : null];

                    $product_details = $product->productDetails()->create($product_detail_arr + $product_attributes_arr + ['size_id' => $size_id,'price' => $price,'quantity' => $quantity]);

                    // $product_details_count = ProductDetails::where('product_id', $product->id)->where('color_id',$product_detail['color_id'])->count();
                    // if($product_details_count <= 1) {
                    // $product_details_count = ProductDetails::where('product_id', $product->id)->where('color_id',$product_detail['color_id'])->count();

                    if($product_detail['sizes'][0]['size_id'] == $size_id) {

                        if (isset($product_detail['media'])) {
                            foreach ($product_detail['media'] as $media)
                            {
                                if(isset($media['image'])){
                                    $image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
                                    $alt_en = $media['image_alt_en']?? null;
                                    $alt_ar = $media['image_alt_ar']?? null;
                                    $product->media()->create(['product_details_id' => $product_details->id ,'color_id' => isset($product_detail['color_id']) ? $product_detail['color_id'] : null, 'media' => $image, 'media_type' => 'image', 'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
                                }
                            }
                        }
                    }


                }
            }


            /// save product images
            ////////////////////////////////////////////////////////////////
            $this->updateProductImages($product);

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.create.success')]);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            dd(11,$e->getMessage(),$e->getLine());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.create.fail')], 422);
        }
    }

    // public function store(ProductRequest $request)
    // {
    //     DB::beginTransaction();
    //     try
    //     {
    //         $slug_ar = str_replace(" ","_",$request->ar['name']);
    //         $slug_en = str_replace(" ","_",$request->en['name']);
    //         $product = Product::create(
    //             array_except($request->validated(),
    //             ['product_details', 'ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description'])
    //             + ['main_category_id' => $request->main_category->id]);
    //             $product->update([
    //                 'en' => ['slug' => $slug_en],
    //                 'ar' => ['slug' => $slug_ar]
    //                 ]);
    //         $product->categories()->attach($request->category_ids);
    //         $meta_data = $product->metas()->create($request->safe()->only(['ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description', 'meta_canonical_tag']));
    //         foreach ($request->product_details as $product_detail)
    //         {
    //             $color_data = isset($product_detail['color_id']) ? Color::find($product_detail['color_id'])->toJson() : null;
    //             $size_data  = isset($product_detail['size_id']) ? Size::find($product_detail['size_id'])->toJson() : null;
    //             $product_details = $product->productDetails()->create(array_except($product_detail, ['media', 'features']) + ['color_data' => $color_data, 'size_data' => $size_data, 'features' => isset($product_detail['features']) ? $product_detail['features'] : null]);
    //             foreach ($product_detail['media'] as $media)
    //             {
    //                 $image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
    //                 $alt_en = $media['image_alt_en']?? null;
    //                 $alt_ar = $media['image_alt_ar']?? null;
    //                 $product->media()->create(['product_details_id' => $product_details->id ,'color_id' => null, 'media' => $image, 'media_type' => 'image', 'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
    //             }
    //         }
    //         DB::commit();
    //         return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.create.success')]);
    //     }
    //     catch (Exception $e)
    //     {
    //         DB::rollBack();
    //         dd($e);
    //         return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.create.fail')], 422);
    //     }
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return (new ProductResource($product))->additional(['status' => 'success', 'message' => '']);
    }

    public function product_details_v2($id)
    {
        $product = Product::findOrFail($id);
        return (new Product2Resource($product))->additional(['status' => 'success', 'message' => '']);
    }

    public function showProduct($id)
    {
        $product = Product::findOrFail($id);
        return (new ShowProductResource($product))->additional(['status' => 'success', 'message' => '']);
    }


    public function summaryProduct($id)
    {
        $product = Product::findOrFail($id);

        $items = $product->productDetails()->get();

        return ProductSummaryResource::collection($items)->additional(['status' => 'success', 'message' => '']);
    }


    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        DB::beginTransaction();

        try
        {
            // if($product->translate('ar')->name != $request->ar['name']){
                $slug_ar = str_replace(" ","_",$request->ar['name']);
                $product->update([
                    'ar' => ['slug' => $slug_ar]
                    ]);
            // }
            // if($product->translate('en')->name != $request->en['name']){
                $slug_en = str_replace(" ","_",$request->en['name']);
                $product->update([
                    'en' => ['slug' => $slug_en],
                    ]);
            // }
            $product_data = $product->update(array_except($request->validated(), ['product_details', 'ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description']) + ['main_category_id' => $request->main_category->id]);

            if($product->metas()->count()){
                $meta_data = $product->metas->update($request->safe()->only(['ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description', 'meta_canonical_tag']));
            }else{
                $meta_data = $product->metas()->create($request->safe()->only(['ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description', 'meta_canonical_tag']));
            }

            if ($request->category_ids)
            {
                $product->categories()->sync($request->category_ids);
            }

            foreach($product->media as $media) {

                $alt_en = $request->image_alt_en?? null;
                $alt_ar = $request->image_alt_ar?? null;

                $media->where([
                    'product_id' => $product->id,
                    'option'=> 'size_guide'
                ])->update(['alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
            }

            $updatedColorsArr = [];

            foreach ($request->product_details as $product_detail)
            {
                $color_id = isset($product_detail['color_id']) ? $product_detail['color_id'] : null;
                $color_data = isset($product_detail['color_id']) ? Color::find($product_detail['color_id'])->toJson() : null;

                foreach ($product_detail['sizes'] as $sizeArr) {

                    $size_data  = isset($sizeArr['size_id']) ? Size::find($sizeArr['size_id'])->toJson() : null;

                    $size_id  = isset($sizeArr['size_id']) ? $sizeArr['size_id'] : null;
                    $price  = isset($sizeArr['price']) ? $sizeArr['price'] : 0;
                    $quantity  = isset($sizeArr['quantity']) ? $sizeArr['quantity'] : 0;

                    $product_detail_arr = array_except($product_detail, ['media', 'features']);
                    $product_attributes_arr = ['color_data' => $color_data, 'size_data' => $size_data, 'features' => isset($product_detail['features']) ? $product_detail['features'] : null];

                    // $product_details = isset($product_detail['product_detail_id']) ? ProductDetails::find($product_detail['product_detail_id']) : null;
                    // info(isset($sizeArr['product_detail_id']) ? $sizeArr['product_detail_id'] : 'sayeds');

                    $product_details = ProductDetails::updateOrCreate(
                        [
                            // 'id' => isset($product_detail['product_detail_id']) ? $product_detail['product_detail_id'] : null
                            'id' => isset($sizeArr['product_detail_id']) ? $sizeArr['product_detail_id'] : null,
                            'color_id'   => $color_id,
                            'product_id' => $id,
                            // 'size_id' => $size_id,
                        ],
                            $product_detail_arr + $product_attributes_arr + ['size_id' => $size_id,'price' => $price,'quantity' => $quantity]
                    );

                    $updatedColorsArr[] = $product_details->id;

                    if (isset($product_detail['media'])) {

                        foreach ($product_detail['media'] as $media)
                        {

                            if (isset($media['image_id']) && isset($media['image']))
                            {
                                $image = ProductMedia::find($media['image_id']);

                                // File::delete(storage_path('app/public/images/products/'.$image->product_id.'/'.$image->product_details_id.'/'.$image->media));

                                $alt_en = $media['image_alt_en']?? null;
                                $alt_ar = $media['image_alt_ar']?? null;

                                $new_image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);

                                $image->update(['media' => $new_image,'color_id' => isset($product_detail['color_id']) ? $product_detail['color_id'] : null, 'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);


                            } else {

                                if(isset($media['image']) && $product_detail['sizes'][0]['size_id'] == $size_id){

                                    $image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
                                    $alt_en = $media['image_alt_en']?? null;
                                    $alt_ar = $media['image_alt_ar']?? null;
                                    $product->media()->create(['product_details_id' => $product_details->id ,'color_id' => isset($product_detail['color_id']) ? $product_detail['color_id'] : null, 'media' => $image, 'media_type' => 'image', 'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);

                                }

                            }
                        }
                    }

                }
            }

            $currentColorsArr = ProductDetails::where('product_id',$product->id)->pluck('id')->toArray();

            $result_arr = array_diff($currentColorsArr, $updatedColorsArr);

            // ProductMedia::whereIn('product_details_id',$result_arr)->delete();
            // ProductDetails::whereIn('id',$result_arr)->delete();

            /// save product images
            ////////////////////////////////////////////////////////////////
            $this->updateProductImages($product);

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.update.success')]);
        }
        catch (Exception $e)
        {
            info($e);
            DB::rollBack();

            dd(22,$e->getMessage(),$e->getLine());

            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.update.fail')], 422);
        }
    }


    // public function update(ProductRequest $request, $id)
    // {
    //     $product = Product::findOrFail($id);

    //     DB::beginTransaction();

    //     try
    //     {
    //         // if($product->translate('ar')->name != $request->ar['name']){
    //             $slug_ar = str_replace(" ","_",$request->ar['name']);
    //             $product->update([
    //                 'ar' => ['slug' => $slug_ar]
    //                 ]);
    //         // }
    //         // if($product->translate('en')->name != $request->en['name']){
    //             $slug_en = str_replace(" ","_",$request->en['name']);
    //             $product->update([
    //                 'en' => ['slug' => $slug_en],
    //                 ]);
    //         // }
    //         $product_data = $product->update(array_except($request->validated(), ['product_details', 'ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description']) + ['main_category_id' => $request->main_category->id]);

    //         if($product->metas()->count()){
    //             $meta_data = $product->metas->update($request->safe()->only(['ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description', 'meta_canonical_tag']));
    //         }else{
    //             $meta_data = $product->metas()->create($request->safe()->only(['ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description', 'meta_canonical_tag']));
    //         }

    //         if ($request->category_ids)
    //         {
    //             $product->categories()->sync($request->category_ids);
    //         }

    //         foreach($product->media as $media) {

    //             $alt_en = $request->image_alt_en?? null;
    //             $alt_ar = $request->image_alt_ar?? null;

    //             $media->where([
    //                 'product_id' => $product->id,
    //                 'option'=> 'size_guide'
    //             ])->update(['alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
    //         }

    //         $updatedColorsArr = [];

    //         foreach ($request->product_details as $product_detail)
    //         {
    //             $color_id = isset($product_detail['color_id']) ? $product_detail['color_id'] : null;
    //             $color_data = isset($product_detail['color_id']) ? Color::find($product_detail['color_id'])->toJson() : null;

    //             foreach ($product_detail['sizes'] as $sizeArr) {

    //                 $size_data  = isset($sizeArr['size_id']) ? Size::find($sizeArr['size_id'])->toJson() : null;

    //                 $size_id  = isset($sizeArr['size_id']) ? $sizeArr['size_id'] : null;
    //                 $price  = isset($sizeArr['price']) ? $sizeArr['price'] : 0;
    //                 $quantity  = isset($sizeArr['quantity']) ? $sizeArr['quantity'] : 0;

    //                 $product_detail_arr = array_except($product_detail, ['media', 'features']);
    //                 $product_attributes_arr = ['color_data' => $color_data, 'size_data' => $size_data, 'features' => isset($product_detail['features']) ? $product_detail['features'] : null];

    //                 // $product_details = isset($product_detail['product_detail_id']) ? ProductDetails::find($product_detail['product_detail_id']) : null;

    //                 $product_details = ProductDetails::updateOrCreate(
    //                     [
    //                         // 'id' => isset($product_detail['product_detail_id']) ? $product_detail['product_detail_id'] : null
    //                         'color_id'   => $color_id,
    //                         'size_id'    => $size_id,
    //                         'product_id' => $id
    //                     ],
    //                         $product_detail_arr + $product_attributes_arr + ['size_id' => $size_id,'price' => $price,'quantity' => $quantity]
    //                 );

    //                 $updatedColorsArr[] = $product_details->id;

    //                 if (isset($product_detail['media'])) {

    //                     foreach ($product_detail['media'] as $media)
    //                     {

    //                         if (isset($media['image_id']))
    //                         {
    //                             $image = ProductMedia::find($media['image_id']);

    //                             // File::delete(storage_path('app/public/images/products/'.$image->product_id.'/'.$image->product_details_id.'/'.$image->media));

    //                             $alt_en = $media['image_alt_en']?? null;
    //                             $alt_ar = $media['image_alt_ar']?? null;

    //                             if(isset($media['image'])) {

    //                                 $new_image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);

    //                                 $image ->update(['media' => $new_image,'color_id' => isset($product_detail['color_id']) ? $product_detail['color_id'] : null, 'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);

    //                             } else {

    //                                 $new_image = $image->media;

    //                                 if($color_id == $product_details->color_id) {
    //                                     $image->update(['product_details_id' => $product_details->id,'media' => $new_image,'color_id' => isset($product_detail['color_id']) ? $product_detail['color_id'] : null, 'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
    //                                 } else {
    //                                     $image->update(['media' => $new_image,'color_id' => isset($product_detail['color_id']) ? $product_detail['color_id'] : null, 'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
    //                                 }

    //                             }

    //                         } else {

    //                             if(isset($media['image']) && $product_detail['sizes'][0]['size_id'] == $size_id){

    //                                 $image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
    //                                 $alt_en = $media['image_alt_en']?? null;
    //                                 $alt_ar = $media['image_alt_ar']?? null;
    //                                 $product->media()->create(['product_details_id' => $product_details->id ,'color_id' => isset($product_detail['color_id']) ? $product_detail['color_id'] : null, 'media' => $image, 'media_type' => 'image', 'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);

    //                             }

    //                         }
    //                     }
    //                 }

    //             }
    //         }

    //         $currentColorsArr = ProductDetails::where('product_id',$product->id)->pluck('id')->toArray();

    //         $result_arr = array_diff($currentColorsArr, $updatedColorsArr);

    //         // ProductMedia::whereIn('product_details_id',$result_arr)->delete();
    //         ProductDetails::whereIn('id',$result_arr)->delete();

    //          /// save product images
    //         ////////////////////////////////////////////////////////////////
    //         $this->updateProductImages($product);

    //         DB::commit();
    //         return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.update.success')]);
    //     }
    //     catch (Exception $e)
    //     {
    //         info($e);
    //         DB::rollBack();

    //         dd(22,$e->getMessage(),$e->getLine());

    //         return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.update.fail')], 422);
    //     }
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(ProductRequest $request, $id)
    // {
    //     $product = Product::findOrFail($id);
    //     DB::beginTransaction();
    //     try
    //     {
    //         // if($product->translate('ar')->name != $request->ar['name']){
    //             $slug_ar = str_replace(" ","_",$request->ar['name']);
    //             $product->update([
    //                 'ar' => ['slug' => $slug_ar]
    //                 ]);
    //         // }
    //         // if($product->translate('en')->name != $request->en['name']){
    //             $slug_en = str_replace(" ","_",$request->en['name']);
    //             $product->update([
    //                 'en' => ['slug' => $slug_en],
    //                 ]);
    //         // }
    //         $product_data = $product->update(array_except($request->validated(), ['product_details', 'ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description']) + ['main_category_id' => $request->main_category->id]);
    //         if($product->metas()->count()){
    //             $meta_data = $product->metas->update($request->safe()->only(['ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description', 'meta_canonical_tag']));
    //         }else{
    //             $meta_data = $product->metas()->create($request->safe()->only(['ar.meta_tag', 'en.meta_tag', 'ar.meta_title', 'en.meta_title', 'ar.meta_description', 'en.meta_description', 'meta_canonical_tag']));
    //         }
    //         if ($request->category_ids)
    //         {
    //             $product->categories()->sync($request->category_ids);
    //         }
    //         foreach($product->media as $media){
    //             $alt_en = $request->image_alt_en?? null;
    //             $alt_ar = $request->image_alt_ar?? null;
    //             $media->where([
    //                 'product_id' => $product->id,
    //                 'option'=> 'size_guide'
    //                 ])->update(['alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
    //         }

    //         foreach ($request->product_details as $product_detail)
    //         {
    //             // return dd($product_media);
    //             // return dd($product->productDetails); //id =570
    //             // return dd($product->productDetails[0]->id);
    //             // return dd($request->product_details);
    //             $color_data = isset($product_detail['color_id']) ? Color::find($product_detail['color_id']) : null;
    //             $size_data  = isset($product_detail['size_id']) ? Size::find($product_detail['size_id']) : null;
    //             $product_details = isset($product_detail['product_detail_id']) ? ProductDetails::find($product_detail['product_detail_id']) : null;
    //             // $product_details = ProductDetails::updateOrCreate(['id' => isset($product_detail['product_detail_id']) ? $product_detail['product_detail_id'] : null], array_except($product_detail, ['media']) + ['product_id' => $product->id, 'color_data' => $color_data, 'size_data' => $size_data, 'features' => isset($product_detail['features']) ? $product_detail['features'] : null]);
    //             $product_details = ProductDetails::updateOrCreate(['id' => isset($product_detail['product_detail_id']) ? $product_detail['product_detail_id'] : null], array_except($product_detail, ['media']) + ['product_id' => $product->id, 'color_data' => $color_data, 'size_data' => $size_data, 'features' => isset($product_detail['features']) ? $product_detail['features'] : null]);
    //             $product_media_not_size = $product->media()->whereNull('option')->get();
    //             // return dd($product_details);
    //             // if (isset($product_detail['media'] ))
    //             // {
    //             //     foreach ($product_detail['media'] as $media)
    //             //     {
    //             //         // info($media);
    //             //         $product_media = ProductMedia::find($media['media_id']);
    //             //         $alt_en = $media['image_alt_en']?? null;
    //             //         $alt_ar = $media['image_alt_ar']?? null;
    //             //         $product_media->update([
    //             //             'alt_en' => $alt_en,
    //             //             'alt_ar' => $alt_ar
    //             //         ]);
    //             //         if (isset($media['image_id']))
    //             //         {
    //             //             $image = ProductMedia::find($media['image_id']);
    //             //             File::delete(storage_path('app/public/images/products/'.$image->product_id.'/'.$image->product_details_id.'/'.$image->media));
    //             //             $new_image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
    //             //             $image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
    //             //             $alt_en = $media['image_alt_en']?? null;
    //             //             $alt_ar = $media['image_alt_ar']?? null;
    //             //             $product->media()->create(['product_details_id' => $product_details->id ,'color_id' => null, 'media' => $image, 'media_type' => 'image', 'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);

    //             //             // $alt_en = $media['image_alt_en']?? ;
    //             //             // $alt_ar = $media['image_alt_ar']?? 'dddd';
    //             //             // logger('alt_en'.$alt_en);
    //             //             // $image->update(['media' => $new_image,'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
    //             //             // foreach($product->media as $product_media){
    //             //             //         $alt_en = $media['image_alt_en']?? null;
    //             //             //         $alt_ar = $media['image_alt_ar']?? null;
    //             //             //         $product_media->update(['alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
    //             //             // }
    //             //         }

    //             //     }
    //             // }else{
    //             //     // logger('inside else');
    //             //     foreach ($product_detail['media'] as $media)
    //             //     {
    //             //         $image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
    //             //         $alt_en = $media['image_alt_en']?? null;
    //             //         $alt_ar = $media['image_alt_ar']?? null;
    //             //         $product->media()->create(['product_details_id' => $product_details->id ,'color_id' => null, 'media' => $image, 'media_type' => 'image', 'alt_en' => $alt_en, 'alt_ar' => $alt_ar]);
    //             //     }
    //             // }
    //             if (isset($product_detail['media']))
    //             {
    //                 foreach ($product_detail['media'] as $media)
    //                 {
    //                     if (isset($media['image_id']))
    //                     {
    //                         $image = ProductMedia::find($media['image_id']);
    //                         File::delete(storage_path('app/public/images/products/'.$image->product_id.'/'.$image->product_details_id.'/'.$image->media));
    //                         $new_image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
    //                         $image ->update(['media' => $new_image]);
    //                     }
    //                     else
    //                     {
    //                         if(isset($media['image'])){
    //                             $image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details->id);
    //                             $product->media()->create(['product_details_id' => $product_details->id ,'color_id' => null, 'media' => $image, 'media_type' => 'image']);
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         DB::commit();
    //         return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.update.success')]);
    //     }
    //     catch (Exception $e)
    //     {
    //         info($e);
    //         DB::rollBack();
    //         return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.update.fail')], 422);
    //     }
    // }


    public function deleteProductDetail($product_id, $color_id)
    {
        $product_details = ProductDetails::where('product_id', $product_id)->where('color_id', $color_id)->get();

        foreach ($product_details as $product_detail) {

            $product_detail_id = $product_detail->id;

            if ($product_detail->delete())
            {
                File::deleteDirectory(storage_path('app/public/images/products/'.$product_id.'/'.$product_detail_id));
            }

        }

        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.delete.success')]);

    }


    public function deleteProductDetailsImage($product_id,$image_id)
    {
        $image = ProductMedia::where('product_id', $product_id)->findOrFail($image_id);

        if (ProductMedia::where('product_details_id', $image->product_details_id)->count() > 1)
        {
            if ($image->delete())
            {
                File::delete(storage_path('app/public/images/products/'.$image->product_id.'/'.$image->product_details_id.'/'.$image->media));
            }

            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.delete.success')]);
        }
        else
        {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.error.details_must_contain_one_image')], 422);
        }
    }


    public function deleteProductDetailsSize($product_id, $color_id,$size_id)
    {
        $product_detail = ProductDetails::where('product_id', $product_id)->where('color_id', $color_id)->where('size_id', $size_id)->firstOrFail();

        $product_detail_id = $product_detail->id;

        if ($product_detail->delete())
        {
            File::deleteDirectory(storage_path('app/public/images/products/'.$product_id.'/'.$product_detail_id));
        }

        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.delete.success')]);

    }


    public function deleteProductDetailsFeature($product_id, $color_id,$feature_id)
    {
        $product_details = ProductDetails::where('product_id', $product_id)->where('color_id', $color_id)->get();

        foreach ($product_details as $product_detail) {

            $json = $product_detail->features; //return an array

            foreach($json as $key => $value) {
                if($value['feature_id'] == $feature_id) {
                 unset($json[$key]);
                }
            }

            if(empty($json)) {
                $features = null;
            } else{
                $features = $json;
            }

            $product_detail->update(['features' => $features]);
        }

        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.delete.success')]);

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

            $flashSaleProducts = FlashSaleProduct::where('product_id',$product->id)->get();

            if($flashSaleProducts != null && $flashSaleProducts->count() > 0) {
                foreach ($flashSaleProducts as $flashSaleProduct) {
                    $flashSaleProduct->flashSale()->delete();
                    $flashSaleProduct->delete();
                }
            }

            $product->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.delete.success')]);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => $e->getLine().'-'.$e->getMessage()], 422);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.delete.fail')], 422);
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
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.update.success')]);
    }



    // public function updateProductImages($product) {

    //     $colorsArr = ProductDetails::where('product_id', '=', $product->id)->pluck('color_id')->toArray();
    //     $colorsArr = array_unique($colorsArr);

    //     $ProductDetails = ProductDetails::where('product_id', '=', $product->id)->orderBy('created_at','asc')->get(['id','product_id','color_id']);

    //     foreach ($ProductDetails as $product_details_row) {

    //         $product_media = ProductMedia::where('product_details_id', '=', $product_details_row->id)->where('color_id',$product_details_row->color_id)->first();

    //         if($product_media == null) {

    //             // $current_product_media = ProductMedia::where('product_id', '=', $product_details_row->product_id)->where('color_id',$product_details_row->color_id)->where('option','!=','size_guide')->orderBy('created_at','asc')->first();

    //             $first_product_color_details = ProductDetails::where('product_id', '=', $product->id)->where('color_id',$product_details_row->color_id)->orderBy('created_at','asc')->first();

    //             info($first_product_color_details);

    //             if($first_product_color_details && $first_product_color_details->media != null) {

    //                 foreach($first_product_color_details->media as $media_row) {

    //                     $product->media()->create([
    //                         'product_details_id' => $product_details_row->id ,
    //                         'color_id' => $product_details_row->color_id,
    //                         'media' => $media_row->media,
    //                         'media_type' => 'image',
    //                         'alt_en' => $media_row->alt_en,
    //                         'alt_ar' => $media_row->alt_ar,
    //                     ]);

    //                     // $image = uploadImg($media['image'], 'products/'.$product->id.'/'.$product_details_row->id);
    //                     // $oldPath = $media_row->media;

    //                     $url     = 'products/'.$product->id.'/'.$first_product_color_details->id;
    //                     $oldPath = 'images/' . $url . "/" . $media_row->media;

    //                     $new_url = 'products/'.$product->id.'/'.$product_details_row->id;
    //                     $newPath = ('storage/images/'.$new_url. '/' . $media_row->media);

    //                     info($oldPath);
    //                     info($newPath);

    //                     if(\Storage::disk('public')->exists($oldPath)) {

    //                         info('ok');

    //                         // $url = 'products/'.$product->id.'/'.$product_details_row->id;
    //                         // $dist = storage_path('app/public/' . $url . "/");

    //                         info('make dir ok - path is '.asset('storage/app/public/images/' .$new_url));

    //                         try {

    //                             // File::makeDirectory(asset('storage/app/public/images/' .$new_url), 0777, true);
    //                             File::makeDirectory(storage_path('app/public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $new_url . DIRECTORY_SEPARATOR), 0777, true);

    //                             // File::makeDirectory(asset('storage/app/public/images/' .$new_url), 0777, true);

    //                             // \File::copy(storage_path('app/'.$oldPath) , $newPath);
    //                             // \File::copy(public_path('storage/'.$oldPath), public_path('storage/'.$new_url));

    //                             // $sourceFilePath = 'storage/app/'.$oldPath;
    //                             // $destinationFolderPath = 'storage/app/'.$new_url;

    //                             // \Storage::copy($sourceFilePath, $destinationFolderPath);



    //                             // // Ensure the source file exists before copying
    //                             // if (Storage::exists($sourceFilePath)) {
    //                             //     // Ensure the destination folder exists, if not create it
    //                             //     if (!Storage::exists($destinationFolderPath)) {
    //                             //         Storage::makeDirectory($destinationFolderPath);
    //                             //     }

    //                             //     // Copy the file
    //                             //     Storage::copy($sourceFilePath, $destinationFolderPath . 'source_file.txt');
    //                             //     echo "File copied successfully.";
    //                             // } else {
    //                             //     echo "Source file does not exist.";
    //                             // }

    //                             // Source image path
    //                             $sourceImagePath = ('storage/'.$oldPath);

    //                             // Destination image path
    //                             $destinationImagePath = ($newPath);

    //                             // Load the source image using Intervention Image
    //                             $image = Image::make($sourceImagePath);

    //                             // Manipulate the image as needed (e.g., resize, crop, apply filters, etc.)
    //                             // Example: Resize the image to 300x200 pixels
    //                             // $image->resize(300, 200);

    //                             // Save the manipulated image to the destination path
    //                             $image->save($destinationImagePath);

    //                         } catch(Exception $e) {
    //                             info($e->getMessage());
    //                             info($e);
    //                         }

    //                     } else {
    //                         info('not ok');
    //                         info($oldPath);


    //                     }


    //                 }
    //             }
    //         }

    //     }

    //     // $ProductDetails = ProductDetails::where('product_id', '=', $product->id)->get(['id','product_id','color_id']);

    //     $all_media = ProductMedia::where('product_id', $product->id)->get();

    //     // $colorsArr = $all_media->pluck('color_id')->toArray();
    //     // $colorsArr = array_unique($colorsArr);

    //     // foreach ($colorsArr as $color_id_row) {

    //     //     $first_media = ProductMedia::where('option','size_guide')->where('product_id', $product->id)->where('color_id',$color_id_row)->first();

    //     //     $url = 'products/'.$product->id.'/'.$product_details_row->id;

    //     //     $dist = storage_path('app/public/' . $url . "/");

    //     //     $path = $dist . '/' . $first_media->media;

    //     //     if($first_media != null && file_exists($path)) {

    //     //     }


    //     // }

    // }


    public function updateProductImages($product) {

        $productDetails = ProductDetails::where('product_id',$product->id)->groupBy('color_id')->orderBy('created_at','asc')->get();

        foreach($productDetails as $first_details) {

            $others = ProductDetails::where('id','!=',$first_details->id)->where('product_id',$first_details->product_id)->where('color_id',$first_details->color_id)->orderBy('created_at','asc')->get();

            $check_medias = ProductMedia::where('product_id',$first_details->product_id)->where('color_id',$first_details->color_id)->where('product_details_id',$first_details->id)->get();

            foreach($others as $row) { 

                if($check_medias != null && $check_medias->count() > 0) {
                    foreach($check_medias as $media_row) {

                        $url     = 'products/'.$row->product_id.'/'.$row->id;
                        $newPath = 'images/' . $url . "/" . $media_row->media;

                        info($newPath .' newPath');

                        if(! \Storage::disk('public')->exists($newPath)) {

                            info($newPath .' file not exists');

                            $current_url     = 'products/'.$media_row->product_id.'/'.$media_row->product_details_id;
                            $currentPath = 'images/' . $current_url . "/" . $media_row->media;

                            info($currentPath .' is current path');
                            info($newPath .' is new path');

                            if(\Storage::disk('public')->exists($currentPath)) {
                                
                                try {

                                    // File::makeDirectory(asset('storage/app/public/images/' .$new_url), 0777, true);
                
                                    $path = 'app/public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR;

                                    if(! File::exists(storage_path($path))) {
                                        File::makeDirectory(storage_path($path), 0777, true);
                                    }

                                    // Source image path
                                    $sourceImagePath = ('storage/'.$currentPath);
                
                                    // Destination image path
                                    $destinationImagePath = ('storage/'.$newPath);

                                    // Copy the file
                                    if (\File::copy($sourceImagePath, $destinationImagePath)) {
                                        
                                        info(['message' => 'File copied successfully']);

                                        $product->media()->updateOrCreate([
                                            'product_id' => $row->product_id ,
                                            'product_details_id' => $row->id ,
                                            'color_id' => $row->color_id,
                                            'media' => $media_row->media,
                                        ],[
                                            'product_id' => $row->product_id ,
                                            'product_details_id' => $row->id ,
                                            'color_id' => $row->color_id,
                                            'media' => $media_row->media,
                                            'media_type' => 'image',
                                            'alt_en' => $media_row->alt_en,
                                            'alt_ar' => $media_row->alt_ar,
                                        ]);

                                    } else {
                                        info(['message' => 'Failed to copy file']);
                                    }

                                    // sleep(1);

                
                                } catch(Exception $e) {
                                    info($e->getMessage());
                                    info($e);
                                }
                            }
                        } else {
                            info($newPath .' file is exists');
                        }
                    }
                }

            }
        }

    }



}
