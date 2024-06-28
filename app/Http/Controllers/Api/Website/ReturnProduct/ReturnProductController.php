<?php

namespace App\Http\Controllers\Api\Website\ReturnProduct;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Website\ReturnProduct\ReturnProductRequest;
use App\Models\{Order,OrderProduct ,ReturnOrder ,User};
use App\Http\Resources\Api\Website\Order\{OrderResource};
use App\Notifications\Api\App\ReturnOrder\ReturnOrderNotification;
use Illuminate\Http\Request;
use App\Http\Resources\Api\Website\ReturnOrder\ReturnOrderResource;

class ReturnProductController extends Controller
{
  public function  store(ReturnProductRequest $request)
  {

    
    $order = Order::where(['id' => $request->order_id, 'user_id' => auth('api')->id(), 'status' => 'admin_delivered'])->firstOrFail();
    if ($order->returnOrder && $order->returnOrder->status == 'waiting') {
      return response()->json(['status' => 'fail', 'message' => trans('app.messages.wait_for_admin_accept_reject_your_request'), 'data' => null], 401);
    }
    
    $product_return_grace_period =  setting('product_return_grace_period') ? setting('product_return_grace_period') : 0;
    if ($order->updated_at->diff(now())->days >   $product_return_grace_period) {
      return response()->json(['status' => 'fail', 'message' => trans('app.messages.return_period_has_expired'), 'data' => null], 401);
    }
    if ($order->orderCoupon != null || $order->flashSaleOrders->count() > 0 || $order->offerOrders->count() > 0) {
      return response()->json(['status' => 'fail', 'message' => trans('app.messages.offered_product_cannot_be_returned'), 'data' => null], 401);
    }

    $orderProducts = $request->order_products;
    foreach ($orderProducts as $orderProduct) {

      $product = OrderProduct::find($orderProduct['order_product_id']);
      if ($product->quantity <  $orderProduct['quantity']) {
        return response()->json(['status' => 'fail', 'message' => trans('app.messages.confirm_the_quantity_to_be_returned'), 'data' => null], 401);
      }
    }
    \DB::beginTransaction();
    try {

      $vat_percentage = $order->orderPriceDetail != null ? $order->orderPriceDetail->vat_percentage : 0;

      $returnOrder =  ReturnOrder::create(array_except($request->validated() + ['user_id'=>auth('api')->id()], ['order_products']));

      foreach ($orderProducts as $orderProduct) {

        $product = OrderProduct::find($orderProduct['order_product_id']);

        $vat_price = round(($product->price * $vat_percentage) / 100);

        $returnOrder->returnOrderProducts()->create([
          'order_product_id' => $orderProduct['order_product_id'],
          'product_detail_id' =>   $product->product_detail_id,
          'quantity' => $orderProduct['quantity'],
          'price' => $product->price,
          'vat_percentage' => $vat_percentage,
          'vat_price' => $vat_price,
          'price_with_vat' =>  $vat_price + $product->price,
        ]);

      }
      \DB::commit();

      $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();
      if ($admins) {
        foreach ($admins as $admin) {
          $admin->notify(new ReturnOrderNotification($order, ['database','fcm']));
        }
      }
              return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.wait_for_administration_to_confirm_the_return_request')]);

    //   return response()->json(['data' => null, 'status' => 'success', 'message' => trans('app.messages.wait_for_administration_to_confirm_the_return_request')]);
    } catch (\Exception $e) {
      \DB::rollback();
      \Log::info($e);
      return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
    }
  }


  public function show($id)
    {
        $returnOrder =   ReturnOrder::where(['id' => $id, 'user_id' => auth('api')->id()])->firstOrFail();

        return response()->json(['data' => new ReturnOrderResource($returnOrder), 'status' => 'success', 'message' => '']);
    }

}
