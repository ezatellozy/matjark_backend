<?php

namespace App\Http\Requests\Api\Provider\Contact;

use App\Http\Requests\Api\ApiMasterRequest;

class ContactReplyRequest extends ApiMasterRequest
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
            'reply'     => 'required|string|between:2,100000',
            'send_type' => 'required|in:fcm,sms,email'
        ];
    }
}
