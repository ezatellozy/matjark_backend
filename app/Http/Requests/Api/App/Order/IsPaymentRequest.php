<?php

namespace App\Http\Requests\Api\App\Order;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class IsPaymentRequest extends ApiMasterRequest
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
        return  [
            'transactionId' =>'required|string|max:255',
        ];

    }
}
