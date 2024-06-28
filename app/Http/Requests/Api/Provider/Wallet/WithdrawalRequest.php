<?php

namespace App\Http\Requests\Api\Provider\Wallet;

use App\Http\Requests\Api\ApiMasterRequest;

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
        return [
            'status'          => 'required|in:accepted,rejected',
            'rejected_reason' => 'nullable|string|min:3|max:255|required_if:status,rejected'
        ];
    }
}
