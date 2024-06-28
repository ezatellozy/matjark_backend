<?php

namespace App\Http\Requests\Api\App\Order;

use App\Http\Requests\Api\ApiMasterRequest;

class CancelOrderRequest extends ApiMasterRequest
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
         'user_cancel_reason'   =>'nullable|string|max:255',
        ];
    }
}
