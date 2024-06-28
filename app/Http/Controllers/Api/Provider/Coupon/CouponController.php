<?php

namespace App\Http\Controllers\Api\Provider\Coupon;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Coupon\CouponRequest;
use App\Http\Resources\Api\Provider\Coupon\{CouponDetailResource, CouponResource, SimpleCouponResource};
use App\Http\Resources\Api\Provider\Order\SimpleOrderResource;
use App\Models\Coupon;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $coupons = Coupon::when($request->start_at, function ($query) use ($request) {
            $query->whereDate('start_at', $request->start_at);
        })->when($request->end_at, function ($query) use ($request) {
            $query->whereDate('end_at', $request->end_at);
        })
            ->when($request->code, function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->code . '%');
            })->latest()->paginate();
        return SimpleCouponResource::collection($coupons)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CouponRequest $request)
    {
        $coupon = Coupon::create($request->validated());
        return CouponResource::make($coupon)->additional(['status' => 'success', 'message' => trans('provider.create.success')]);
    }
    public function couponAdditionData($id)
    {
        $coupon = Coupon::findOrFail($id);
        return CouponDetailResource::make($coupon)->additional(['status' => 'success', 'message' => '']);
    }
    public function couponAdditionDataOrders($id)
    {
        $coupon = Coupon::findOrFail($id);
        $orders = Order::where('status', 'admin_delivered')->whereHas('orderCoupon', function ($q) use ($id) {
            $q->where('coupon_id', $id);
        })->paginate();
        return SimpleOrderResource::collection($orders)->additional(['status' => 'success', 'message' => '']);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $coupon = Coupon::findOrFail($id);
        return CouponResource::make($coupon)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CouponRequest $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        try {
            $coupon->update($request->validated());
            return CouponResource::make($coupon)->additional(['status' => 'success', 'message' => trans('provider.update.success')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.update.fail')], 422);
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
        $coupon = Coupon::findOrFail($id);
        if ($coupon->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.success')]);
        }
        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
    }
}
