<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Resources\Json\JsonResource;

class MessagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'chat_id' => $this->chat_id,
            'order_id' => $this->order_id,
            'message_id' => $this->id,
            'message_position' => $this->sender_id == auth('api')->id() ? 'me' : 'other',
            'message_sender' => $this->sender_id,
            'sender_data' => [
                'id' => $this->sender_id,
                'fullname' => $this->sender->fullname,
                'phone' => $this->sender->phone,
                'image' => $this->sender->avatar,
            ],
            'receiver_data' => [
                'id' => $this->receiver_id,
                'fullname' => optional($this->receiver)->fullname,
                'phone' => optional($this->receiver)->phone,
                'image' => optional($this->receiver)->avatar,
            ],
            'message' =>  $this->message,
            'message_type' => $this->message_type,
            'read_at' =>  (string) optional($this->read_at)->format("Y-m-d h:i A"),
            'created_at' => (string) $this->created_at->format("Y-m-d h:i A"),
        ];
    }
}
