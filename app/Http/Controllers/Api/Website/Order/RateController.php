<?php

namespace App\Http\Controllers\Api\Website\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Website\Order\{RateRequest};
use App\Http\Resources\Api\Website\Rate\{RateResource, SimpleRateResource};
use App\Models\{OrderRate, ProductDetails};
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function index()
    {
        $rates = OrderRate::where('user_id', auth('api')->id())->orderBy('id', 'desc')->paginate(12);
        return (SimpleRateResource::collection($rates))->additional(['status' => 'success', 'message' => '']);
    }


    public function store(RateRequest $request, $order_id)
    {
        try {


            $product_details = ProductDetails::find($request->product_detail_id);

            $arr = ['user_id' => auth('api')->id(), 'order_id' => $order_id];

            if($product_details != null) {
                $arr['product_id'] = $product_details->product_id;
            }

            $rate =   OrderRate::create($request->validated() + $arr);
            // $avg = rate_avg($request->product_detail_id);
            // $rate->productDetail()->update([
            //     'rate_avg' => $avg,
            // ]);
            return response()->json(['data' => null, 'status' => 'success', 'message' => trans('app.messages.added_successfully')]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }
}
