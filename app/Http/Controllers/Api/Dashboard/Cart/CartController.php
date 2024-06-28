<?php

namespace App\Http\Controllers\Api\Dashboard\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Dashboard\Cart\CartDetailsResource;
use App\Http\Resources\Api\Dashboard\Cart\CartResource;
use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $to   = Carbon::now()->endOfMonth()->format('Y-m-d');

        $cart = Cart::Has('cartProducts')

        ->when($request->get_data_by != null, function ($query) use($request,$from , $to) {
            if($request->get_data_by == 'this_month') {
                $query->whereBetween('created_at', [$from , $to]);
            }
        })
        ->when($request->product_id, function ($query) use ($request) {
            $query->whereHas('cartProducts',function($cartProducts) use ($request) {
                $cartProducts->where('product_id',$request->product_id);
            });
        })
        ->latest()->paginate();
        return CartResource::collection($cart)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cart = Cart::findOrFail($id);
        return CartDetailsResource::make($cart)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
