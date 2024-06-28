<?php

namespace App\Http\Controllers\Api\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\App\Home\{CategoryFeatureResource, SimpleProductResource};
use App\Http\Resources\Api\App\Product\{ProductResource};
use App\Http\Resources\Api\App\Rate\RateResource;
use App\Models\{Category, OrderRate, Product, ProductDetails};
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getCategoryFeatures($id)
    {
        $category = Category::findOrFail($id);

        return (new CategoryFeatureResource($category))->additional([
            'status' => 'success',
            'data'   => [
                'sort' => [
                    ['key' => 'top_rate', 'value' => trans('app.sort.top_rate')],
                    ['key' => 'price_low_to_high', 'value' => trans('app.sort.price_low_to_high')],
                    ['key' => 'price_high_to_low', 'value' => trans('app.sort.price_high_to_low')],
                    ['key' => 'new_arrival', 'value' => trans('app.sort.new_arrival')],
                    ['key' => 'most_popular', 'value' => trans('app.sort.most_popular')],
                    ['key' => 'recommended', 'value' => trans('app.sort.recommended')],
                ]
            ],
            'message' => ''
        ]);
    }

    public function index(Request $request)
    {
        $category = Category::where(['is_active' => true, 'id' => $request->category_id])->firstOrFail();
        $categories  =  lastLevel($category);
        switch ($request->sorted) {
            case 'top_rate':
                $column = 'rate_avg';
                $fun    = 'desc';
                break;

            case 'price_low_to_high':
                $column = 'price';
                $fun    = 'asc';
                break;

            case 'price_high_to_low':
                $column = 'price';
                $fun    = 'desc';
                break;

            case 'new_arrival':
                $column = 'created_at';
                $fun    = 'desc';
                break;

            case 'most_popular':
                $column = 'sold';
                $fun    = 'desc';
                break;

            default:
                $column = 'id';
                $fun    = 'desc';
                break;
        }

        $products = Product::where('is_active', true)->whereHas('categoryProducts', function ($q) use ($categories, $category) {
            if (count($categories) >0) {
                $q->whereIn('category_id', $categories->pluck('id')->toArray());
            } else {
                $q->where('category_id', $category->id);
            }
        })
            ->whereHas('productDetails', function ($query) use ($request) {
                $query->when($request->from_price, function ($query) use ($request) {
                    $query->where('price', '>=', $request->from_price);
                })->when($request->to_price, function ($query) use ($request) {
                    $query->where('price', '<=', $request->to_price);
                })->when($request->color_ids, function ($query) use ($request) {
                    $query->whereIn('color_id', is_array($request->color_ids) ? $request->color_ids : json_decode($request->color_ids, true));
                })->when($request->size_ids, function ($query) use ($request) {
                    $query->whereIn('size_id', is_array($request->size_ids) ? $request->size_ids : json_decode($request->size_ids, true));
                })->when($request->features, function ($query) use ($request) {
                    foreach (is_array($request->features) ? $request->features : json_decode($request->features, true) as $feature) {
                        $query->whereJsonContains('features', ['feature_id' => isset($feature['feature_id']) ? (string) $feature['feature_id'] : null, 'value_id' => isset($feature['value_id']) ? (string) $feature['value_id'] : null]);
                    }
                });
            })
     
                ->join('product_details', 'products.id', '=', 'product_details.product_id')
                    ->orderBy('product_details.' . $column, $fun)
                    ->groupBy('product_id')
                    ->select('products.*')->distinct()
  
                    ->when($request->sorted == 'recommended', function ($query) {
                        $query->inRandomOrder();
                    })
            ->paginate(10);

        return (SimpleProductResource::collection($products))->additional(['status' => 'success', 'message' => '']);
    }
    public function show($id)
    {
        $product = Product::where(['is_active' => true, 'id' => $id])->whereHas('productDetails', function ($q) {
            $q->where('quantity', '>=', 0);
            $q->groupBy('color_id');
        })->firstOrFail();

        $categories = thirdLavels(Category::find($product->main_category_id));
        
        $products =  Product::where(['is_active' => true])->where('id', '!=', $id)
            ->whereHas('categoryProducts', function ($q) use ($categories) {
                $q->whereIn('category_id', $categories ?  $categories->pluck('id')->toArray() : []);
            })
            ->whereHas('productDetails', function ($q) {
                $q->where('quantity', '>=', 0);
            })
            ->orderBy('ordering', 'asc')->paginate(6);

        return (SimpleProductResource::collection($products))->additional(['status' => 'success', 'message' => '', 'product' => new ProductResource($product)]);
    }
    public function productDetailRates($product_detail_id)
    {
        $productDetail = ProductDetails::findOrFail($product_detail_id);
        $productDetailRates = OrderRate::where(['status' => 'accepted', 'product_detail_id' => $product_detail_id])->get();
        $rates =  OrderRate::where(['product_detail_id' => $product_detail_id, 'status' => 'accepted'])->count();
        $ratesData  = [
            'total_rating' => (int) OrderRate::where('product_detail_id', $product_detail_id)->count(),
            '5_stars' =>  $rates   > 0  ?  (float) ((OrderRate::where(['status' => 'accepted', 'product_detail_id' => $product_detail_id, 'rate' => 5])->count() /  $rates) * 100) : 0,
            '4_stars' => $rates   > 0  ? (float)((OrderRate::where(['status' => 'accepted', 'product_detail_id' => $product_detail_id])->where('rate', '<', 5)->where('rate', '>=', 4)->count() / $rates) * 100) : 0,
            '3_stars' => $rates > 0 ? (float)((OrderRate::where(['status' => 'accepted', 'product_detail_id' => $product_detail_id])->where('rate', '<', 4)->where('rate', '>=', 3)->count() / $rates) * 100) : 0,
            '2_stars' => $rates > 0  ? (float)((OrderRate::where(['status' => 'accepted', 'product_detail_id' => $product_detail_id])->where('rate', '<', 3)->where('rate', '>=', 2)->count() / $rates) * 100) : 0,
            '1_stars' => $rates > 0  ? (float)((OrderRate::where(['status' => 'accepted', 'product_detail_id' => $product_detail_id])->where('rate', '<', 2)->where('rate', '>=', 1)->count() / $rates) * 100) : 0,
            'rate_avg' => (float)$productDetail->rate_avg,

        ];
        return (RateResource::collection($productDetailRates))->additional(['status' => 'success', 'message' => '',  'rating' => $ratesData]);
    }

    public function search(Request $request)
    {
        
      $products =   Product::where('is_active', true)->where(function($q)use($request){
                $q->whereTranslationLike('name', '%' . $request->name . '%')->orWhereTranslationLike('desc', '%' . $request->name . '%');
            })->paginate(16);
        // $products = Product::where('is_active', true)->whereTranslationLike('name', '%' . $request->name . '%')->orWhereTranslationLike('desc', '%' . $request->name . '%')->paginate(6);
        return (SimpleProductResource::collection($products))->additional(['status' => 'success', 'message' => '']);
    }
}
