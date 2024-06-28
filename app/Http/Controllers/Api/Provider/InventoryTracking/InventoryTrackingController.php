<?php

namespace App\Http\Controllers\Api\Provider\InventoryTracking;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Provider\InventoryTracking\InventoryTrackingResource;
use App\Http\Resources\Api\Provider\Product\{SimpleProductResource};
use App\Models\{FlashSale, FlashSaleProduct, Product, ProductDetails};
use Illuminate\Http\Request;

class InventoryTrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $flashSale         = FlashSale::where('end_at', '>', now())->pluck('id')->toArray();
        // $flashSale_product = FlashSaleProduct::whereIn('flash_sale_id', $flashSale)->select('*', \DB::raw("SUM(quantity) as count"))->groupBy('product_detail_id')->get();
        // dd($flashSale_product);

        $product_details = ProductDetails::orderBy('quantity','asc')->where('quantity', '<=', setting('minimum_stock') ?? 0)
        ->when($request->from_price, function ($query) use ($request) {
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
        })->paginate();

        return InventoryTrackingResource::collection($product_details)->additional(['status' => 'success', 'message' => '']);

        // $products = Product::when($request->keyword, function ($query) use ($request) {
        //     $query->whereTranslationLike('name', '%' . $request->keyword . '%')
        //         ->orWhereTranslationLike('desc', '%' . $request->keyword . '%');
        // })->whereHas('productDetails', function ($query) use ($request) {
        //     $query->where('quantity', '<=', setting('minimum_stock') ?? 0)
        //     ->when($request->from_price, function ($query) use ($request) {
        //         $query->where('price', '>=', $request->from_price);
        //     })->when($request->to_price, function ($query) use ($request) {
        //         $query->where('price', '<=', $request->to_price);
        //     })->when($request->from_quantity, function ($query) use ($request) {
        //         $query->where('quantity', '>=', $request->from_quantity);
        //     })->when($request->to_quantity, function ($query) use ($request) {
        //         $query->where('quantity', '<=', $request->to_quantity);
        //     })->when($request->quantity, function ($query) use ($request) {
        //         $query->where('quantity', $request->quantity);
        //     })->when($request->color_id, function ($query) use($request) {
        //         $query->where('color_id', $request->color_id);
        //     })->when($request->size_id, function ($query) use($request) {
        //         $query->where('size_id', $request->size_id);
        //     })->when($request->features, function ($query) use ($request) {
        //         foreach ($request->features as $feature)
        //         {
        //             $query->whereJsonContains('features', ['feature_id' => isset($feature['feature_id']) ? $feature['feature_id'] : null, 'value_id' => isset($feature['value_id']) ? $feature['value_id'] : null]);
        //         }
        //     });
        // })->latest()->paginate();

        // return SimpleProductResource::collection($products)->additional(['status' => 'success', 'message' => '']);
    }

    // public function flashSaleQuantity($product_detail_id)
    // {
    //     $flash_sale_quantity = FlashSale::where('end_at', '>', now())->whereHas('flashSaleProducts', function ($query) use ($product_detail_id) {
    //         $query->where('product_detail_id', $product_detail_id);
    //     })->sum('quantity');

    //     return $flash_sale_quantity;
    // }
}
