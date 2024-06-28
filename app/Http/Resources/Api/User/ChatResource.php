<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        if ($this->sender_id == auth('api')->id()) {
            $client_data = [
                'id' => $this->receiver_id,
                'fullname' => optional($this->receiver)->fullname,
                'image' => optional($this->receiver)->avatar,
                'phone' => optional($this->receiver)->phone,
            ];
        }else{
            $client_data = [
                'id' => $this->sender_id,
                'fullname' => $this->sender->fullname,
                'image' => $this->sender->avatar,
                'phone' => $this->sender->phone,
            ];
        }

        return [
            'chat_id' => $this->id,
            'order_id' => $this->order_id,
            'client_data' => $client_data,
            // 'message_position' => $this->sender_id == auth('api')->id() ? 'me' : 'other',
            'sender_data' => [
                'id' => $this->sender_id,
                'fullname' => $this->sender->fullname,
                'image' => $this->sender->avatar,
                'phone' => $this->sender->phone,
            ],
            'receiver_data' => [
                'id' => $this->receiver_id,
                'fullname' => optional($this->receiver)->fullname,
                'phone' => optional($this->receiver)->phone,
                'image' => optional($this->receiver)->avatar,
            ],
            'can_send_message' => !($this->order->finished_at || in_array($this->order->order_status,['client_cancel','admin_cancel','driver_cancel'])),
            'start_location' => optional($this->order)->start_location_data,
            'end_location' => optional($this->order)->end_location_data,
            'message_type' => $this->message_type ?? "text",
            'last_message' => $this->last_message,
            'read_at' => (string)optional($this->read_at)->format('Y-m-d h:i A'),
            'created_at' => (string)optional($this->created_at)->format('Y-m-d'),
            'messages' => $this->when($request->is('*/chats/*') , MessagesResource::collection($this->messages()->paginate(10)))
        ];
    }
}
