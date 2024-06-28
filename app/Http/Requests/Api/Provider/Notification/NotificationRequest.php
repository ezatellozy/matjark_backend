<?php

namespace App\Http\Requests\Api\Provider\Notification;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Validation\Rule;

class NotificationRequest extends ApiMasterRequest
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
            'all'        => 'required|in:0,1',
            'user_ids'   => 'nullable|array|required_if:all,0',
            'user_ids.*' => 'nullable|exists:users,id,user_type,client',
            'title'      => 'required|string|between:3,200',
            'body'       => 'required|string|between:3,10000',
        ];
    }
}
