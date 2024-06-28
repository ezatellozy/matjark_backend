<?php

namespace App\Http\Requests\Api\Website\Wallet;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Withdrawal;
use Illuminate\Http\Exceptions\HttpResponseException;


class WithdrawalRequest extends ApiMasterRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $walletValue =  auth()->guard('api')->user()->wallet ? (float)auth()->guard('api')->user()->wallet->balance  : 0.0;
        $checkAmount  = ($walletValue >= $this->amount) ? true : false;
        $data = [
            'amount' => 'required|numeric|min:1',
            'iban' => 'required|max:225',
            'bank_name' => 'required|max:225',
            'city' => 'nullable|max:225',
            'branch' => 'nullable|max:225',
            'account_number' => 'required|max:225',

        ];
        if ($checkAmount == false) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.no_balance_withdrawal'),
                'data' => null,
            ], 422));
        }
        if (Withdrawal::where(['user_id' => auth('api')->id(), 'status' => 'pending'])->first() != null) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' =>  trans('app.messages.you_have_another_withdrawals_request_still_pending'),
                'data' => null,
            ], 422));
        }
        return $data;
    }
}
