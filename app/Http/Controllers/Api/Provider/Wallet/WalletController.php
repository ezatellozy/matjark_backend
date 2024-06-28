<?php

namespace App\Http\Controllers\Api\Provider\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Wallet\WalletChargeRequest;
use App\Http\Resources\Api\Provider\Wallet\WalletTransactionResource;
use App\Models\User;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function transactions(Request $request, $id)
    {
        $user = User::whereHas('wallet')->findOrFail($id);

        $transactions = $user->walletTransactions()->latest()->paginate();

        return response()->json([
            'status' => 'success',
            'data' => [
                'balance'      => $user->wallet ? (float) $user->wallet->balance : 0.0,
                'transactions' => WalletTransactionResource::collection($transactions),
            ],
            'message' => '',
        ], 200);
    }

    public function charge(WalletChargeRequest $request, $id)
    {
        $user = User::whereHas('wallet')->findOrFail($id);

        if ($request->type == 'withdrawal' && $request->amount > $user->wallet->balance) {
            return response()->json(['data' => null, 'status' => 'fail', 'message' => trans('provider.message.amount_bigger_than_wallet')], 422);
        }

        return $this->balance($request, $user, $request->type);
    }

    public function balance($request, $user, $type)
    {
        DB::beginTransaction();
        try {
            $balance       = $user->wallet->balance;
            $balance_after = $type == 'deposit' ? $balance + $request->amount : $balance - $request->amount;
            $bank_data     =  json_encode(array_except($request->validated(), ['amount', 'type', 'city']));

            WalletTransaction::create([
                'user_id'        => $user->id,
                'wallet_id'      => $user->wallet->id,
                'balance_before' => $balance,
                'balance_after'  => $balance_after,
                'amount'         => $request->amount,
                'type'           => $type,
                'bank_data'      => $type == 'withdrawal' ? $bank_data : null,
                'added_by'       => auth('api')->id()
            ]);

            switch ($type) {
                case "deposit":
                    $user->wallet()->increment('balance', $request->amount);
                    break;
                case "withdrawal":
                    $user->wallet()->decrement('balance', $request->amount);
                    break;
            }

            DB::commit();
            return response()->json(['data' => null, 'status' => 'success', 'message' => trans('provider.wallet.' . '.' . $type, ['amount' => $request->amount])]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.error.fail')], 422);
        }
    }
}
