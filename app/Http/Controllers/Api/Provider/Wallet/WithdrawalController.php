<?php

namespace App\Http\Controllers\Api\Provider\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Wallet\WithdrawalRequest;
use App\Http\Resources\Api\Provider\Wallet\WithdrawalResource;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $withdraw_requests = Withdrawal::when($request->status, function ($query) use($request) {
            $query->where('status', $request->status);
        })
        ->latest()->paginate(10);

        return WithdrawalResource::collection($withdraw_requests)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        return (new WithdrawalResource($withdrawal))->additional(['status' => 'success', 'message' => '']);
    }

    public function changeStatus(WithdrawalRequest $request, $id)
    {
        $withdrawal = Withdrawal::where('status', 'pending')->findOrFail($id);

        DB::beginTransaction();
        try {
            switch ($request->status) {
                case 'accepted':
                    $balance = optional($withdrawal->user)->wallet->balance;

                    if($balance < $withdrawal->amount)
                    {
                        return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.withdrawal.user_wallet_less_than_requested_amount')]);
                    }

                    $withdrawal->update(['status' => 'accepted', 'admin_id' => auth('api')->id()]);
                    $balance_after = $balance - $withdrawal->amount;
                    $bank_data = [
                        'bank_name'      => $withdrawal->bank_name,
                        'branch'         => $withdrawal->branch,
                        'account_number' => $withdrawal->account_number,
                        'iban'           => $withdrawal->iban
                    ];
                    WalletTransaction::create([
                        'user_id'        => $withdrawal->user_id,
                        'wallet_id'      => optional($withdrawal->user)->wallet->id,
                        'balance_before' => $balance,
                        'balance_after'  => $balance_after,
                        'amount'         => $withdrawal->amount,
                        'type'           => 'withdrawal',
                        'bank_data'      => json_encode($bank_data),
                    ]);
                    optional($withdrawal->user)->wallet()->decrement('balance', $withdrawal->amount);
                    break;
                case 'rejected':
                    $withdrawal->update(['status' => $request->status, 'rejected_reason' => $request->rejected_reason]);
                    break;
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.error.fail')], 422);
        }
    }
}
