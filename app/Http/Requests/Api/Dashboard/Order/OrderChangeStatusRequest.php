<?php

namespace App\Http\Requests\Api\Dashboard\Order;

use App\Http\Requests\Api\ApiMasterRequest;

class OrderChangeStatusRequest extends ApiMasterRequest
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
            'status'          => 'required|in:admin_accept,admin_rejected,admin_cancel,admin_shipping,admin_delivered',
            'rejected_reason' => 'nullable|string|max:255|min:2'
        ];
    }
}
