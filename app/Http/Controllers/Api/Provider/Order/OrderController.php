<?php

namespace App\Http\Controllers\Api\Provider\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Order\OrderChangeStatusRequest;
use App\Http\Resources\Api\Provider\Order\OrderProductResource;
use App\Http\Resources\Api\Provider\Order\OrderResource;
use App\Http\Resources\Api\Provider\Order\SimpleOrderResource;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Models\ProductDetails;
use App\Models\WalletTransaction;
use App\Notifications\Api\Provider\Order\AdminAcceptNotification;
use App\Notifications\Api\Provider\Order\AdminChangeStatusNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{

    public function order_items()
    {
        $user = auth()->guard('api')->user();

        try {

            $user_products = Product::where('added_by_id',$user->id)->pluck('id')->toArray();
            
            $products_details_arr = ProductDetails::whereIn('product_id',$user_products)->pluck('id')->toArray();
            
            $items = $products_details_arr != null && ! empty($products_details_arr) ? OrderProduct::whereIn('product_detail_id',$products_details_arr)->get() : [];
           
            return OrderProductResource::collection($items)->additional(['status' => 'success', 'message' => '']);

        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.error.fail')], 422);
        }
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = Order::when($request->status, function ($query) use($request) {
            $query->where('status', $request->status);
        })->when($request->is_payment, function ($query) use ($request) {
            $query->where('is_payment', $request->is_payment);
        })->when($request->transactionId, function ($query) use ($request) {
            $query->where('transactionId', $request->transactionId);
        })->when($request->pay_type, function ($query) use ($request) {
            $query->where('pay_type', $request->pay_type);
        })->when($request->from or $request->to, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
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
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.error.cant_accept_this_order_becouse_the_client_not_payed')], 422);
        }
        $order->update(['status' => 'admin_accept', 'order_status_times' => ['admin_accept' => date("Y-m-d H:i")]]);
        Notification::send($order->client, new AdminChangeStatusNotification($order, ['database', 'fcm'], 'admin_accept'));
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.order.accept_success')]);
    }

    public function rejectOrder($order, $reason = null)
    {
        $this->returnQuantityAndPrice($order);
        $order->update(['status' => 'admin_rejected', 'admin_reject_reason' => $reason, 'order_status_times' => ['admin_rejected' => date("Y-m-d H:i")]]);
        Notification::send($order->client, new AdminChangeStatusNotification($order, ['database', 'fcm'], 'admin_rejected'));
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.order.reject_success')]);
    }

    public function shippingOrder($order)
    {
        $order->update(['status' => 'admin_shipping', 'order_status_times' => ['admin_shipping' => date("Y-m-d H:i")]]);
        Notification::send($order->client, new AdminChangeStatusNotification($order, ['database', 'fcm'], 'admin_shipping'));
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.order.shipping_success')]);
    }

    public function deliveredOrder($order)
    {
        $order->update(['status' => 'admin_delivered', 'is_payment' => 'paid', 'order_status_times' => ['admin_delivered' => date("Y-m-d H:i")]]);
        $order->orderProducts()->join('product_details', 'order_products.product_detail_id', '=', 'product_details.id')->select('order_products.quantity as quantity')->increment('product_details.sold', 1);
        Notification::send($order->client, new AdminChangeStatusNotification($order, ['database', 'fcm'], 'admin_delivered'));
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.order.delivered_success')]);
    }

    public function cancelOrder($order, $reason = null)
    {
        $this->returnQuantityAndPrice($order);
        $order->update(['status' => 'admin_cancel', 'admin_reject_reason' => $reason, 'order_status_times' => ['admin_cancel' => date("Y-m-d H:i")]]);
        Notification::send($order->client, new AdminChangeStatusNotification($order, ['database', 'fcm'], 'admin_cancel'));
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.order.cancel_success')]);
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
            DB::commit();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.error.something_went_error')], 422);
        }
    }
}
