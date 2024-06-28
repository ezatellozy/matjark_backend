<?php

namespace App\Http\Requests\Api\Dashboard\Wallet;

use App\Http\Requests\Api\ApiMasterRequest;

class WalletChargeRequest extends ApiMasterRequest
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
        return [
            'amount'         => 'required|numeric|min:1',
            'type'           => 'required|string|in:deposit,withdrawal',
            'bank_name'      => 'required_if:type,withdrawal|max:225',
            'branch'         => 'required_if:type,withdrawal|max:225',
            'account_number' => 'required_if:type,withdrawal|max:225',
            'iban'           => 'required_if:type,withdrawal|max:225',
            'city'           => 'nullable|max:225',
        ];
    }
}
