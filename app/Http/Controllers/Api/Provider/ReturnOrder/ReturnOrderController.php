<?php

namespace App\Http\Controllers\Api\Provider\ReturnOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\ReturnOrder\{ReturnOrderChangeStatusRequest, ReturnOrderChangeStatusSecondStepRquest};
use App\Http\Resources\Api\Provider\ReturnOrder\{IndexReturnOrderResource, ReturnOrderResource, ReturnOrderProductResource};
use App\Models\{ProductDetails, ReturnOrder, ReturnOrderProduct, WalletTransaction};
use App\Notifications\Api\Provider\ReturnOrder\ReturnOrderNotification;
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
        })->get();

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
            $returnOrderProduct->update(['status' => $return_order_product['status'], 'reject_reason' => array_key_exists('reject_reason', $return_order_product) ? $return_order_product['reject_reason'] : null]);
        }

        if ($returnOrder->status == 'waiting')
        {
            $returnOrder->update(['admin_id' => auth('api')->id()]);
            
            if (! $returnOrder->returnOrderProducts()->where('status', '!=', 'rejected')->first())
            {
                $returnOrder->update(['status' => 'finished']);
            }
        }

        Notification::send($returnOrder->user, new ReturnOrderNotification($returnOrder, ['database', 'fcm']));

        return response()->json(['status' => 'success', 'data' => null, 'message' => '']);
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

    public function changeReturnOrderProductStatus(ReturnOrderChangeStatusSecondStepRquest $request)
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
                $total_price += ($received_product->quantity * $received_product->price);
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
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.error.something_went_error')], 422);
        }
    }
}
