<?php

namespace App\Http\Controllers\Api\Dashboard\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Order\OrderChangeStatusRequest;
use App\Http\Resources\Api\Dashboard\Order\OrderResource;
use App\Http\Resources\Api\Dashboard\Order\SimpleOrderResource;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Order;
use App\Models\ProductDetails;
use App\Models\WalletTransaction;
use App\Notifications\Api\Dashboard\Order\AdminChangeStatusNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\Coupon;
use Carbon\Carbon;

class OrderController extends Controller
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

        $orders = Order::when($request->status, function ($query) use($request) {
            $query->where('status', $request->status);
        })
        ->when($request->get_data_by != null, function ($query) use($request,$from , $to) {
            if($request->get_data_by == 'this_month') {
                $query->whereBetween('created_at', [$from , $to]);
            } elseif($request->get_data_by == 'total_revenue_this_month') {
                $query->hereBetween('created_at', [$from , $to])->where('is_payment', 'paid');
            }
        })
        ->when($request->is_payment, function ($query) use ($request) {
            $query->where('is_payment', $request->is_payment);
        })->when($request->transactionId, function ($query) use ($request) {
            $query->where('transactionId', $request->transactionId);
        })->when($request->pay_type, function ($query) use ($request) {
            $query->where('pay_type', $request->pay_type);
        })->when($request->from or $request->to, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        })
        ->when($request->product_id, function ($query) use ($request) {
            $query->whereHas('orderProducts',function($orderProducts) use ($request) {
                $orderProducts->where('product_id',$request->product_id);
            });
        })->latest()->paginate();

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
        $order = Order::findOrFail($id);
        return (new OrderResource($order))->additional(['status' => 'success', 'message' => '']);
    }

    public function changeStatus(OrderChangeStatusRequest $request, $id)
    {
        switch ($request->status) {
            case 'admin_accept':
                $order = Order::where('status', 'pending')->findOrFail($id);
                $response = $this->acceptOrder($order);
                break;

            case 'admin_rejected':
                $order = Order::where('status', 'pending')->findOrFail($id);
                $response = $this->rejectOrder($order, $request->rejected_reason);
                break;

            case 'admin_shipping':
                $order = Order::where('status', 'admin_accept')->findOrFail($id);
                $response = $this->shippingOrder($order);
                break;

            case 'admin_delivered':
                $order = Order::where('status', 'admin_shipping')->findOrFail($id);
                $response = $this->deliveredOrder($order);
                break;

            case 'admin_cancel':
                $order = Order::whereNotIn('status', ['pending', 'admin_rejected', 'client_cancel', 'client_finished'])->findOrFail($id);
                $response = $this->cancelOrder($order, $request->rejected_reason);
                break;

            default:
                $response = response()->json(['status' => 'fail', 'data' => null, 'message' => '']);
                break;
        }

        return $response;
    }

    public function acceptOrder($order)
    {
        if (in_array($order->pay_type, ['wallet', 'card']) && $order->is_payment == 'not_paid')
        {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.error.cant_accept_this_order_becouse_the_client_not_payed')], 422);
        }
        $order->update(['status' => 'admin_accept', 'order_status_times' => ['admin_accept' => date("Y-m-d H:i")]]);
        Notification::send($order->client, new AdminChangeStatusNotification($order, ['database', 'fcm'], 'admin_accept'));
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.order.accept_success')]);
    }

    public function rejectOrder($order, $reason = null)
    {
        $order->update(['status' => 'admin_rejected', 'admin_reject_reason' => $reason, 'order_status_times' => ['admin_rejected' => date("Y-m-d H:i")]]);
        Notification::send($order->client, new AdminChangeStatusNotification($order, ['database', 'fcm'], 'admin_rejected'));
        $client = \App\Models\User::where('id',$order->user_id)->first();
        if(($order->pay_type=='card' && $order->is_payment == 'paid') || ($order->pay_type=='wallet' && $order->is_payment == 'paid') ){
             \App\Models\WalletTransaction::create([
                    'user_id' => $order->user_id,
                    'wallet_id' => @$client->wallet->id,
                    'order_id' => $order->id,
                    'balance_before' => @$client->wallet->balance,
                    'balance_after' => (@$client->wallet->balance + $order->orderPriceDetail->total_price),
                    'amount' => $order->orderPriceDetail->total_price,
                    'type' => 'charge',
                    'status'    => 'accepted',
                ]);
                @$client->wallet()->update(['balance' => (@$client->wallet->balance + $order->orderPriceDetail->total_price)]);
        }

        $this->returnQuantityAndPrice($order);

        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.order.reject_success')]);
    }

    public function shippingOrder($order)
    {
        $order->update(['status' => 'admin_shipping', 'order_status_times' => ['admin_shipping' => date("Y-m-d H:i")]]);
        Notification::send($order->client, new AdminChangeStatusNotification($order, ['database', 'fcm'], 'admin_shipping'));
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.order.shipping_success')]);
    }

    public function deliveredOrder($order)
    {
        $order->update(['status' => 'admin_delivered', 'is_payment' => 'paid', 'order_status_times' => ['admin_delivered' => date("Y-m-d H:i")]]);
        $order->orderProducts()->join('product_details', 'order_products.product_detail_id', '=', 'product_details.id')->select('order_products.quantity as quantity')->increment('product_details.sold', 1);
        Notification::send($order->client, new AdminChangeStatusNotification($order, ['database', 'fcm'], 'admin_delivered'));
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.order.delivered_success')]);
    }

    public function cancelOrder($order, $reason = null)
    {
        $order->update(['status' => 'admin_cancel', 'admin_reject_reason' => $reason, 'order_status_times' => ['admin_cancel' => date("Y-m-d H:i")]]);
        Notification::send($order->client, new AdminChangeStatusNotification($order, ['database', 'fcm'], 'admin_cancel'));

        $this->returnQuantityAndPrice($order);

        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.order.cancel_success')]);
    }

    public function returnQuantityAndPrice($order)
    {
        DB::beginTransaction();
        try
        {
            if (in_array($order->pay_type, ['wallet', 'card']) && $order->is_payment == 'paid')
            {
                if (optional(@$order->client->wallet) and optional($order->orderPriceDetail)->total_price)
                {
                    $client = $order->client;
                    WalletTransaction::create([
                        'user_id'        => $client->id,
                        'wallet_id'      => $client->wallet->id,
                        'order_id'       => $order->id,
                        'balance_before' => $client->wallet->balance,
                        'balance_after'  => ($client->wallet->balance + $order->orderPriceDetail->total_price),
                        'amount'         => $order->orderPriceDetail->total_price,
                        'type'           => 'charge',
                    ]);

                    $order->client->wallet->increment('balance', $order->orderPriceDetail->total_price);
                }
            }
            foreach ($order->orderProducts as $order_product)
            {
                ProductDetails::where('id', $order_product->product_detail_id)->increment('quantity', $order_product->quantity);
                FlashSaleProduct::where(['id' => $order_product->flash_sale_product_id])->decrement('sold', $order_product->quantity);
            }

            if ($order->has('orderCoupon')) {
                $coupon = Coupon::findOrFail($order->orderCoupon->coupon_id);
                $coupon->decrement('num_of_used', 1);
                logger($coupon);
            }
            DB::commit();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.error.something_went_error')], 422);
        }
    }
}
