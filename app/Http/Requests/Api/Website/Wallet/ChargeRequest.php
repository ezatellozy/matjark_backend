<?php

namespace App\Http\Requests\Api\Website\Wallet;

use App\Http\Requests\Api\ApiMasterRequest;


class ChargeRequest extends ApiMasterRequest
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
                'amount' =>  'required|numeric|min:1',
                'url' => 'required|url',
                // 'transaction_id' => 'required|string',
             ];
    }
}
