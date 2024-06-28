<?php

namespace App\Http\Controllers\Api\App\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\Wallet\{ChargeRequest, WithdrawalRequest};
use App\Http\Resources\Api\App\Wallet\{WalletTransactionResource};
use App\Models\{Wallet, WalletTransaction, Withdrawal};
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $transactions = WalletTransaction::where(['user_id' => auth('api')->id()])->take(4)->orderBy('id', 'desc')->get();
        // return response()->json(['status' => 'success', 'message' => '', 'data' => [
        //     'transactions' => WalletTransactionResource::collection($transactions),
        //     'balance' => auth()->guard('api')->user()->wallet ? (float)auth()->guard('api')->user()->wallet->balance  : 0.0,
        // ]], 200);
        return response()->json(['status' => 'success', 'message' => '', 'data' => WalletTransactionResource::collection($transactions),   'balance' => auth()->guard('api')->user()->wallet ? (float)auth()->guard('api')->user()->wallet->balance  : 0.0 ,    'currency' => 'SAR'
    ]);

    }
    public function transactions()
    {
        $transactions = WalletTransaction::where(['user_id' => auth('api')->id()])->orderBy('id', 'desc')->get();
        return response()->json(['status' => 'success', 'message' => '', 'data' => WalletTransactionResource::collection($transactions)]);
    }

    public function charge(ChargeRequest $request)
    {
        \DB::beginTransaction();
        try {
            $userWallet = Wallet::where('user_id', auth('api')->id())->firstOrCreate(['user_id' => auth('api')->id()]);
            $charge  = WalletTransaction::create(
                [
                    'user_id' => auth('api')->id(),
                    'type' => 'charge',
                    'balance_before' => $userWallet->balance ?$userWallet->balance :0, 
                    'balance_after' => $userWallet->balance + $request->amount,
                    'amount' => $request->amount,
                    'wallet_id' => $userWallet->id,
                    'transaction_id' => $request->transaction_id,
                    'status' => 'accepted'
                ]
            );
            $userWallet->update([
                'balance' => $userWallet->balance + $request->amount
            ]);
            \DB::commit();
            return response()->json(['status' => 'success', 'message' => trans('app.messages.charge_wallet_successfully'), 'data' => new WalletTransactionResource($charge)]);
        } catch (\Exception $e) {
            \DB::rollback();
            dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }

    public function cacheOutRequest(WithdrawalRequest $request)
    {
        try {
            $cacheOut   =  Withdrawal::create($request->validated() +  [
                'user_id' => auth('api')->id(),
                'currency' => 'SAR',
                'status' => 'pending',
            ]);
            return response()->json(['status' => 'success', 'message' => trans('app.messages.wait_withdrawal'), 'data' => null]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }
}
