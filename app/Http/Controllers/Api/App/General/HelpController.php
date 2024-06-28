<?php

namespace App\Http\Controllers\Api\App\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\App\Address\AddressResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\FavouriteProduct;
use Illuminate\Http\Request;

class HelpController extends Controller
{

    public function countValues(Request $request)
    {
        $data = [];
        if (auth()->guard('api')->user() != null) {
            $address  =   Address::where(['user_id' => auth('api')->id(), 'is_default' => 1])->first();
            $data += [
                'count_of_cart' =>    Cart::where(['user_id' => auth('api')->id(), 'guest_token' =>  null])->first() ?Cart::where(['user_id' => auth('api')->id(), 'guest_token' =>  null])->first()->cartProducts()->sum('quantity'):null,
                'count_of_favourites' =>   FavouriteProduct::where(['user_id' => auth('api')->id(), 'guest_token' =>  null])->count(),
                'count_of_notification' => auth()->guard('api')->user()->notifications()->where('read_at', null)->count(),
                'default_address' =>$address ?  new AddressResource($address): null,
                'whatsapp' => (string)setting('whatsapp'),

            ];
        }
        elseif ($request->guest_token) {
            $data += [
                'count_of_cart' =>    Cart::where(['user_id' => null, 'guest_token' =>  $request->guest_token])->first() ?Cart::where(['user_id' => null, 'guest_token' =>  $request->guest_token])->first()->cartProducts()->sum('quantity') :null,
                'count_of_favourites' =>    FavouriteProduct::where(['user_id' => null, 'guest_token' =>  $request->guest_token])->count(),
                'count_of_notification' => null,
                'default_address' => null,
                'whatsapp' => (string)setting('whatsapp'),

            ];
        }

        return  response()->json([
            'data' =>   $data,
            'status' => 'success',
            'message' =>  ''
        ]);
    }
}
