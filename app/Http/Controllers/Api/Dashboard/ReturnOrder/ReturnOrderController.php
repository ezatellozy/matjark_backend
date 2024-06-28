<?php

namespace App\Http\Controllers\Api\Dashboard\ReturnOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\ReturnOrder\{ReturnOrderChangeStatusRequest, ReturnOrderChangeStatusSecondStepRequest};
use App\Http\Resources\Api\Dashboard\ReturnOrder\{IndexReturnOrderResource, ReturnOrderResource, ReturnOrderProductResource};
use App\Models\{ProductDetails, ReturnOrder, ReturnOrderProduct, Wallet, WalletTransaction};
use App\Notifications\Api\Dashboard\ReturnOrder\ReturnOrderNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ReturnOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $return_orders = ReturnOrder::whereHas('returnOrderProducts', function ($query) use ($request) {

            $query->when($request->status, function ($query) use($request) {
                $query->where('status', $request->status);
            });

        })->when($request->product_id, function ($query) use ($request) {

            $product_detailsArr = ProductDetails::where('product_id',$request->product_id)->pluck('id')->toArray();

            $query->whereHas('returnOrderProducts',function($returnOrderProducts) use ($product_detailsArr) {
                $returnOrderProducts->whereIn('product_detail_id',$product_detailsArr);
            });

        })->latest()->paginate();

        return IndexReturnOrderResource::collection($return_orders)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $return_order = ReturnOrder::findOrFail($id);
        return (new ReturnOrderResource($return_order))->additional(['status' => 'success', 'message' => '']);
    }




    public function changeStatus(ReturnOrderChangeStatusRequest $request)
    {
        $returnOrder = ReturnOrder::where('status', 'waiting')->findOrFail($request->return_order_id);

        foreach ($request->return_order_products as $return_order_product)
        {
            $returnOrderProduct = ReturnOrderProduct::findOrFail($return_order_product['return_order_product_id']);

            $return_order_product_status = $return_order_product['status'];

            $returnOrderProduct->update([
                'status' => $return_order_product['status'],
                'reject_reason' => array_key_exists('reject_reason', $return_order_product) ? $return_order_product['reject_reason'] : null
            ]);
        }

        if ($returnOrder->status == 'waiting')
        {
            $returnOrder->update(['admin_id' => auth('api')->id()]);

            // if (! $returnOrder->returnOrderProducts()->where('status', '!=', 'rejected')->first())
            // {
            //     $returnOrder->update(['status' => 'finished']);
            // }

            if ($returnOrder->returnOrderProducts()->count() == $returnOrder->returnOrderProducts()->where('status', 'accepted')->count())
            {
                $returnOrder->update(['status' => 'finished']);

                if($returnOrder->order) {

                    $returnOrder->order->update(['status' => 'returned_order']);

                    $this->updateOrderStockAndWallet($returnOrder);

                }

            } elseif ($returnOrder->returnOrderProducts()->count() == $returnOrder->returnOrderProducts()->where('status', 'rejected')->count()) {

                $returnOrder->update(['status' => 'rejected']);

                $this->updateOrderStockAndWallet($returnOrder);

            } else {

                // if($returnOrder->order) {

                //     $returnOrder->order->update(['status' => 'returned_order']);

                //     // $this->updateOrderStockAndWallet($returnOrder);

                // }

                $returnOrder->update(['status' => 'is_replied']);

                $this->updateOrderStockAndWallet($returnOrder);

            }
        }

        try {
            Notification::send($returnOrder->user, new ReturnOrderNotification($returnOrder, ['database', 'fcm']));
        } catch (Exception $e) {

        }

        return response()->json(['status' => 'success', 'data' => null, 'message' => '']);
    }



    public function updateOrderStockAndWallet($returnOrder) {

        DB::beginTransaction();
        try
        {
            $received_products = $returnOrder->returnOrderProducts()->where('status', 'accepted')->get();

            $total_price = 0;

            foreach ($received_products as $received_product)
            {
                // info('quantity '. $received_product->quantity);
                // info('price_with_vat '. $received_product->price_with_vat);

                $total_price += ($received_product->quantity * $received_product->price_with_vat);
                ProductDetails::where('id', $received_product->product_detail_id)->increment('quantity', $received_product->quantity);
            }

            // info('first v1');

            if (optional($returnOrder->order)->is_payment == 'paid')
            {
                // info('first v2 - ' .@$returnOrder->user->wallet);
                // info('first v3 - ' .$total_price);

                if (optional(@$returnOrder->user->wallet) and $total_price)
                {
                    $user = $returnOrder->user;

                    // info('first v4 - ' .$user);

                    if($user) {

                        // info('first v5 - ' .$user->wallet);

                        if($user->wallet) {

                            //   info('first v6  ');

                            $wallet = $user->wallet;

                            // info('wallet');
                            // info($wallet);

                            WalletTransaction::create([
                                'user_id'        => $user->id,
                                'wallet_id'      => $wallet->id,
                                'order_id'       => $returnOrder->order_id,
                                'balance_before' => $wallet->balance,
                                'balance_after'  => ($wallet->balance + $total_price),
                                'amount'         => $total_price,
                                'type'           => 'charge',
                            ]);

                            // $wallet->increment('balance', $total_price);

                        } else {

                            // info('first v7  ');

                            $wallet = Wallet::create([
                                'user_id' => $user->id,
                                'balance' => 0,
                            ]);

                            // info('wallet');
                            // info($wallet);

                            WalletTransaction::create([
                                'user_id'        => $user->id,
                                'wallet_id'      => $wallet->id,
                                'order_id'       => $returnOrder->order_id,
                                'balance_before' => 0,
                                'balance_after'  => $total_price,
                                'amount'         => $total_price,
                                'type'           => 'charge',
                            ]);

                            // $wallet->increment('balance', $total_price);

                        }

                        // info('total is '.$total_price);
                        $wallet->increment('balance', $total_price);

                        // $returnOrder->user->wallet->increment('balance', $total_price);
                    }
                }
            }

            DB::commit();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            info($e);
            // return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.error.something_went_error')], 422);
        }
    }



    public function getReturnOrderProductByStatus(Request $request, $id)
    {
        $returnOrder = ReturnOrder::where('status', 'waiting')->findOrFail($id);
        $returnOrderProduct = $returnOrder->returnOrderProducts()->where('status' , $request->status)->get();
        if ($returnOrder->status == 'waiting')
        {
            $returnOrder->update(['admin_id' => auth('api')->id()]);
        }
        return ReturnOrderProductResource::collection($returnOrderProduct)->additional(['status' => 'success', 'message' => '']);
    }

    public function changeReturnOrderProductStatus(ReturnOrderChangeStatusSecondStepRequest $request)
    {
        $returnOrder = ReturnOrder::where('status', 'waiting')->findOrFail($request->return_order_id);
        foreach ($request->return_order_products as $return_order_product)
        {
            $returnOrderProduct = ReturnOrderProduct::where('status', 'accepted')->findOrFail($return_order_product['return_order_product_id']);
            $returnOrderProduct->update(['status' => $return_order_product['status'], 'reject_reason' => array_key_exists('reject_reason', $return_order_product) ? $return_order_product['reject_reason'] : null]);
        }

        if ($returnOrder->status == 'waiting')
        {
            $returnOrder->update(['admin_id' => auth('api')->id(), 'status' => 'finished']);
        }

        Notification::send($returnOrder->user, new ReturnOrderNotification($returnOrder, ['database', 'fcm']));
        return response()->json(['status' => 'success', 'data' => null, 'message' => '']);
    }

    public function returnQuantityAndPrice($returnOrder)
    {
        DB::beginTransaction();
        try
        {
            $received_products = $returnOrder->returnOrderProducts()->where('status', 'received')->get();
            $total_price = 0;

            foreach ($received_products as $received_product)
            {
                $total_price += ($received_product->quantity * $received_product->price_with_vat);
                ProductDetails::where('id', $received_product->product_detail_id)->increment('quantity', $received_product->quantity);
            }

            if (optional($returnOrder->order)->is_payment == 'paid')
            {
                if (optional(@$returnOrder->user->wallet) and $total_price)
                {
                    $user = $returnOrder->user;
                    WalletTransaction::create([
                        'user_id'        => $user->id,
                        'wallet_id'      => $user->wallet->id,
                        'order_id'       => $returnOrder->id,
                        'balance_before' => $user->wallet->balance,
                        'balance_after'  => ($user->wallet->balance + $total_price),
                        'amount'         => $total_price,
                        'type'           => 'charge',
                    ]);

                    $returnOrder->user->wallet->increment('balance', $total_price);
                }
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
