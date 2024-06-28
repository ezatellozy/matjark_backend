<?php

namespace App\Http\Requests\Api\Chat;

use App\Http\Requests\Api\ApiMasterRequest;

class ChatRequest extends ApiMasterRequest
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
        if ($this->message_type == 'image') {
            $message = 'required|image|mimes:png,jpg,jpeg';
        }elseif (in_array($this->message_type ,['file','sound'])) {
            $message = 'required|file|mimes:docx,doc,docs,rar,zip,mp3,wma,aac,wav,flac,m4a,pdf|max:20480';
        }elseif($this->message_type == 'order'){
            $message = 'required|exists:orders,id';
        }else{
            $message = 'required|string|between:1,250';
        }
        return [
            'message_type' => 'required|in:text,image,order,location,file,sound',
            'message' => $message,
            'receiver_id' => 'required|exists:users,id,deleted_at,NULL',
            'order_id' => 'required|exists:orders,id,deleted_at,NULL',
        ];
    }
}
