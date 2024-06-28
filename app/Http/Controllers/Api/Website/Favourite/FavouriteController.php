<?php

namespace App\Http\Controllers\Api\Website\Favourite;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Website\Favourite\FavRequest;
use App\Http\Resources\Api\Website\Product\SimpleProductDetailsResource;
use App\Models\{FavouriteProduct,ProductDetails};
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function index(Request $request)
    {
        if (auth('api')->id() != null) {

            $products = ProductDetails::whereHas('favProductDetails', function ($q) use ($request) {
                $q->where(['user_id' => auth('api')->id(), 'guest_token' => null]);
            })->get();
                        // dd('user' ,        $products );

            // ->join('favourite_products', 'product_details.id', '=', 'favourite_products.product_detail_id')->orderBy('favourite_products.created_at' ,'desc')->select('product_details.*')->get();
        } elseif( $request->guest_token !=  null) {
            $products = ProductDetails::whereHas('favProductDetails', function ($q) use ($request) {
                $q->where(['user_id' => null, 'guest_token' => $request->guest_token]);
            })->get();
            // dd('guest' ,        $products );
            // ->join('favourite_products', 'product_details.id', '=', 'favourite_products.product_detail_id')->orderBy('favourite_products.created_at' ,'desc')->select('product_details.*')->get();
        }else{
             $products = [];
        }
        return (SimpleProductDetailsResource::collection($products))->additional(['status' => 'success', 'message' => '']);

    }
    public function fav(FavRequest $request)
    {
        $type =  auth()->guard('api')->user() ? 'user' : 'guest';
        // dd($type);
        switch ($type) {
            case 'user':
                if (FavouriteProduct::where(['user_id' => auth('api')->id(), 'guest_token' => null,'product_detail_id' => $request->product_detail_id])->exists()) {
                    FavouriteProduct::where(['user_id' => auth('api')->id(),  'guest_token' => null,'product_detail_id' => $request->product_detail_id])->delete();
                    $is_fav = false;
                } else {
                    FavouriteProduct::create(['user_id' => auth('api')->id(),  'guest_token' => null, 'product_detail_id' => $request->product_detail_id]);
                    $is_fav = true;
                }

                break;
            case 'guest':
                if (FavouriteProduct::where(['guest_token' => $request->guest_token,  'user_id' => null,'product_detail_id' => $request->product_detail_id])->exists()) {
                    FavouriteProduct::where(['guest_token' => $request->guest_token,  'user_id' => null ,'product_detail_id' => $request->product_detail_id])->delete();
                    $is_fav = false;
                } else {
                    FavouriteProduct::create(['guest_token' => $request->guest_token,  'user_id' => null,'product_detail_id' => $request->product_detail_id]);
                    $is_fav = true;
                }
                break;
        }

        return response()->json(['data' => ['is_fav' => $is_fav], 'status' => 'success', 'message' => trans('app.messages.edited_successfully')]);

    }
}
