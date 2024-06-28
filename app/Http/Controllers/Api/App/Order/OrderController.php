<?php

namespace App\Http\Controllers\Api\App\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\Order\{CancelOrderRequest, IsPaymentRequest, OrderRequest, ReorderRequest};
use App\Http\Resources\Api\App\Order\{OrderResource, SimpleOrderResource,OrderStatusResource};
use App\Models\{Cart, FlashSaleProduct, Order, ProductDetails, User, WalletTransaction};
use App\Notifications\Api\App\Order\{ClientCancelNotification, ClientFinishNotification, PendingStatusNotification};
use App\Traits\OrderOperation;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use OrderOperation;

    public function index(Request $request)
    {
        //pending , admin_accept , admin_cancel , client_cancel  , admin_shipping , admin_delivered , client_finished
        $orders = Order::where('user_id', auth('api')->id())->when($request->status, function ($q) use ($request) {
            switch ($request->status) {
                case 'delivered':
                    $q->where('status', 'admin_delivered');
                    break;
                case 'shipped':
                    $q->where('status', 'admin_shipping');
                    break;
                case 'unpaid':
                    $q->where(['status' => 'admin_shipping', 'is_payment' => 'not_paid']);
                    break;
                case 'processing':
                    $q->whereIn('status', ['pending', 'admin_accept']);
                    break;

                case 'canceled':
                    $q->whereIn('status', ['admin_cancel', 'client_cancel', 'admin_rejected']);
                    break;
                case 'return_products':
                    $q->where('status', 'admin_delivered');
                    $q->whereHas('returnOrder');
                    break;
            }
        })->orderBy('id', 'desc')->paginate(6);
        
        $status = [
            [
                'key' => 'delivered',
                'value' => trans('app.messages.order.delivered'),
                'status' => ['admin_delivered']
            ],
            [
                'key' => 'shipped',
                'value' => trans('app.messages.order.shipped'),
                'status' => ['admin_shipping']

            ],
            [
                'key' => 'unpaid',
                'value' => trans('app.messages.order.unpaid'),
                'status' => ['admin_shipping']

            ],
            [
                'key' => 'processing',
                'value' => trans('app.messages.order.processing'),
                'status' => ['pending', 'admin_accept']

            ],
            [
                'key' => 'canceled',
                'value' => trans('app.messages.order.canceled'),
                'status' => ['client_cancel', 'admin_cancel', 'admin_rejected']

            ],
            [
                'key' => 'return_products',
                'value' => trans('app.messages.order.return_products'),
                'status' => []

            ],
        ];
        return response()->json(['data' => SimpleOrderResource::collection($orders), 'status' => 'success', 'message' => '', 'available_status' => $status]);
        // return (SimpleOrderResource::collection($orders))->additional(['status' => 'success', 'message' => '']);
    }

    public function show($id)
    {
        $order = Order::where(['id' => $id, 'user_id' => auth('api')->id()])->firstOrFail();
        return response()->json(['data' => new OrderResource($order), 'status' => 'success', 'message' => '']);
    }

    public function store(OrderRequest $request)
    {
        \DB::beginTransaction();
        try {
            $order = $this->addProduct($request);
            if ($order->pay_type == 'wallet') {
                WalletTransaction::create([
                    'user_id' => auth('api')->id(),
                    'wallet_id' => auth()->guard('api')->user()->wallet->id,
                    'order_id' => $order->id,
                    'balance_before' => auth()->guard('api')->user()->wallet->balance,
                    'balance_after' => (auth()->guard('api')->user()->wallet->balance - $order->orderPriceDetail->total_price),
                    'amount' => $order->orderPriceDetail->total_price,
                    'type' => 'buying',
                ]);
                auth()->guard('api')->user()->wallet()->update(['balance' => (auth()->guard('api')->user()->wallet->balance - $order->orderPriceDetail->total_price)]);
                $order->update(['is_payment' => 'paid']);
            }
            \DB::commit();
            $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();
            if ($admins) {
                foreach ($admins as $admin) {
                    $admin->notify(new PendingStatusNotification($order, ['database', 'broadcast']));
                }
            }
            Cart::where('user_id',auth()->guard('api')->user()->id)->delete();
            return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.added_successfully')]);
        } catch (\Exception $e) {
            \Log::info($e);
            \DB::rollback();

            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }


    public function isPayment(IsPaymentRequest $request, $id)
    {
        $order = Order::where(['id' => $id, 'user_id' => auth('api')->id()])->firstOrFail();
        if ($order->pay_type != 'card') {
            return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.confirm_the_payment_method')]);
        }
        if ($order->is_payment == 'paid') {
            return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.the_order_has_been_prepaid')]);
        }
        \DB::beginTransaction();
        try {
            $order->update(['is_payment' => 'paid', 'transactionId' => $request->transactionId]);
            \DB::commit();
            return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.paid_successfully')]);
        } catch (\Exception $e) {
            \Log::info($e);
            \DB::rollback();
            // dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }


    public function cancel(CancelOrderRequest $request, $order_id)
    {
        $order = Order::where(['id' => $order_id, 'user_id' => auth('api')->id(), 'status' => 'pending'])->firstOrFail();
        try {
            $order->update([
                'status' => 'client_cancel',
                'order_status_times' => ['client_cancel' => date("Y-m-d H:i")],
                'user_cancel_reason' => $request->user_cancel_reason,
            ]);
            // add total price to client wallet if payment   / wallet , card and paid


            if (in_array($order->pay_type, ['card', 'wallet']) && $order->is_payment == 'paid') {
                WalletTransaction::create([
                    'user_id' => auth('api')->id(),
                    'wallet_id' => auth()->guard('api')->user()->wallet->id,
                    'order_id' => $order->id,
                    'balance_before' => auth()->guard('api')->user()->wallet->balance,
                    'balance_after' => (auth()->guard('api')->user()->wallet->balance + $order->orderPriceDetail->total_price),
                    'amount' => $order->orderPriceDetail->total_price,
                    'type' => 'charge',
                ]);
                auth()->guard('api')->user()->wallet()->update(['balance' => (auth()->guard('api')->user()->wallet->balance + $order->orderPriceDetail->total_price)]);
            }
            // return quantity
            $orderProducts = $order->orderProducts;
            if ($orderProducts) {
                foreach ($orderProducts as $order_product) {
                    ProductDetails::where('id', $order_product->product_detail_id)->increment('quantity', $order_product->quantity);
                    FlashSaleProduct::where(['id' => $order_product->flash_sale_product_id])->decrement('sold', $order_product->quantity);
                }
            }

            // notification for admin
            $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();
            if ($admins) {
                foreach ($admins as $admin) {
                    // $admin->notify(new ClientCancelNotification($order, ['database', 'fcm']));
                    $admin->notify(new ClientCancelNotification($order, ['database']));
                }
            }
            return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.cancel_successfully')]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }


    public function finished(Request $request, $order_id)
    {
        $order = Order::where(['id' => $order_id, 'user_id' => auth('api')->id(), 'status' => 'admin_delivered', 'is_payment' => 'paid'])->firstOrFail();
        try {
            $order->update([
                'status' => 'client_finished',
                'order_status_times' => ['client_finished' => date("Y-m-d H:i")],
            ]);
            //  notify for admin
            $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();
            if ($admins) {
                foreach ($admins as $admin) {
                    $admin->notify(new ClientFinishNotification($order, ['database']));
                }
            }
            return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.finished_successfully')]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }


    public function reorder(ReorderRequest $request)
    {
        \DB::beginTransaction();
        try {
            $order = Order::where(['id' => $request->order_id, 'user_id' => auth('api')->id()])->firstOrFail();
            $orderItems = $order->orderProducts;
            $cart = Cart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->firstOrCreate([
                'user_id' => auth('api')->id(),
                'guest_token' => null,
            ]);
            foreach ($orderItems as $orderItem) {
                if ($cart->cartProducts()->where(['product_detail_id' => $orderItem->product_detail_id, 'offer_id' => $orderItem->offer_id, 'flash_sale_product_id' => $orderItem->flash_sale_product_id])->exists()) {
                    $cart->cartProducts()->where(['product_detail_id' => $orderItem->product_detail_id, 'offer_id' => $orderItem->offer_id, 'flash_sale_product_id' => $orderItem->flash_sale_product_id])->increment('quantity', $orderItem->quantity);
                } else {
                    $cart->cartProducts()->create([
                        'product_detail_id' => $orderItem->product_detail_id,
                        'quantity' => $orderItem->quantity,
                        'offer_id' => $orderItem->offer_id,
                        'flash_sale_product_id' => $orderItem->flash_sale_product_id
                    ]);
                }
            }
            \DB::commit();
            return response()->json(['data' => null, 'status' => 'success', 'message' => trans('app.messages.send_order_to_cart_successfully')]);
        } catch (\Exception $e) {
            \DB::rollback();
            dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }

    }


    public function showOrderStatus($id)
    {
        // return dd($order);
        $order = Order::where(['id' => $id])->firstOrFail();
        return response()->json(['data' => new OrderStatusResource($order), 'status' => 'success', 'message' => '']);
    }

}
