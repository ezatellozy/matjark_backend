<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\{Chat , User , Message , Order};
use Illuminate\Http\Request;
use App\Http\Requests\Api\Chat\{ChatRequest};
use App\Events\Chat\{ChatEvent , MessageIsSeenEvent};
use App\Http\Resources\Api\User\{ChatResource,MessagesResource};
use App\Notifications\General\{FCMNotification};

class ChatController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $chats = Chat::join('messages', 'messages.chat_id',"=",'chats.id')->where(function($q){
            $q->where('chats.sender_id',auth('api')->id())->orWhere('chats.receiver_id',auth('api')->id());
        })->when($request->filter,function($q)use($request){
            $q->where(function($q)use($request){
                $q->whereHas('sender',function($q)use($request){
                    $q->where('users.fullname',"LIKE","%{$request->filter}%")->where('users.id','<>',auth('api')->id());
                })->orWhereHas('receiver',function($q)use($request){
                    $q->where('users.fullname',"LIKE","%{$request->filter}%")->where('users.id','<>',auth('api')->id());
                })->orWhereHas('messages',function($q)use($request){
                    $q->where('messages.message',"LIKE","%{$request->filter}%");
                });
            });
        })->latest('messages.created_at')->select('chats.*')->distinct('chats.id')->paginate(10);
        return ChatResource::collection($chats)->additional(['status' => 'success', 'message' => '']);
    }

    public function show($order_id,$receiver_id)
    {
        $order = Order::where(function ($q) {
            $q->where('client_id',auth('api')->id())->orWhere('driver_id',auth('api')->id());
        })->findOrFail($order_id);

        $driver = User::whereIn('user_type',['client','driver'])->findOrFail($receiver_id);

        $chat = Chat::where(function($q)use($receiver_id){
            $q->where(['sender_id' => auth('api')->id() , 'receiver_id' => $receiver_id])->orWhere(['receiver_id' => auth('api')->id(), 'sender_id' => $receiver_id]);
        })->where(['order_id' => $order_id])->first();

        if (!$chat) {
             $chat = Chat::create(['sender_id' => auth('api')->id() ,'order_id' => $order_id , 'receiver_id' => $receiver_id]);
        }
        if (!$chat->read_at) {
            $chat->update(['read_at' => now()]);
        }
        $chat->messages()->where(['receiver_id' => auth('api')->id() , 'order_id' => $order_id])->whereNull('read_at')->update(['read_at' => now()]);

        return MessagesResource::collection($chat->messages()->get())->additional(['status' => 'success', 'message' => '','chat_id' => $chat->id]);
    }

    public function store(ChatRequest $request)
    {
        $order = Order::where(function ($q) {
            $q->where('client_id',auth('api')->id())->orWhere('driver_id',auth('api')->id());
        })->findOrFail($request->order_id);

        if ($order->finished_at || in_array($order->order_status,['client_cancel','admin_cancel','driver_cancel'])) {
            return response()->json(['status' => 'fail' , 'message' => trans('api.messages.cant_open_this_chat'),'data' => null],404);
        }

        $chat = Chat::where(function($q)use($request){
            $q->where(['sender_id' => auth('api')->id() , 'receiver_id' => $request->receiver_id])->orWhere(['receiver_id' => auth('api')->id(), 'sender_id' => $request->receiver_id]);
        })->where(['order_id' => $request->order_id])->first();

       if ($chat) {
           $chat->update(['sender_id' => auth('api')->id() , 'receiver_id' => ($chat->sender_id == auth('api')->id() ? $chat->receiver_id : $chat->sender_id) ,'message_type' => $request->message_type]);
       }else{
           $chat = Chat::create(['sender_id' => auth('api')->id() ,'order_id' => $request->order_id ,'receiver_id' => $request->receiver_id ,'message_type' => $request->message_type]);
       }

       $chat->messages()->whereNull('read_at')->where(['receiver_id' => auth('api')->id() , 'order_id' => $request->order_id])->update(['read_at' => now()]);

       $message = $chat->messages()->create(['sender_id' => auth('api')->id() ,'receiver_id' => $chat->sender_id == auth('api')->id() ? $chat->receiver_id : $chat->sender_id , 'message' => $request->message,'message_type' => $request->message_type ,'order_id' => $chat->order_id]);

       $chat->update(['last_message' => $message->message, 'message_type' => $message->message_type , 'read_at' => null]);
       $fcm_data =[
           'title' => trans('dashboard.fcm.new_chat_title'),
           'body' => trans('dashboard.fcm.new_chat_body',['sender' => auth()->guard('api')->user()->fullname , 'order_id' => $chat->order_id]),
           'notify_type' => 'new_chat' ,
           'chat_id' => $chat->id,
           'order_id' => $chat->order_id,
           'start_location' => $chat->order->start_location_data,
           'end_location' => $chat->order->end_location_data,
           'sender_id' => $chat->sender_id,
           'receiver_name' => $chat->sender->name,
           'receiver_data' => [
               'id' => $chat->sender_id,
               'fullname' => $chat->sender->fullname,
               'phone' => $chat->sender->phone,
               'avatar' => $chat->sender->avatar,
           ],
           //Chat Object
           'message_object' => $this->getMessageObject($message),
       ];
       if ($chat->sender_id == auth('api')->id()) {
           pushFcmNotes($fcm_data,[$chat->receiver_id]);
           // $chat->receiver->notify(new FCMNotification($fcm_data,['fcm']));
       }else{
           pushFcmNotes($fcm_data,[$chat->sender_id]);
           // $chat->sender->notify(new FCMNotification($fcm_data,['fcm']));
       }
       data_set($message , 'message_position' , ($message->sender_id == auth('api')->id() ? 'me' : 'other'));
       broadcast(new ChatEvent($message));
       return (new MessagesResource($message))->additional(['status' => 'success', 'message' => trans('dashboard.messages.success_send'),'chat_id' => $chat->id]);
    }

    public function destroy($id)
    {
        $chat = Chat::where(function($q){
            $q->where('sender_id',auth('api')->id())->orWhere('receiver_id',auth('api')->id());
        })->findOrFail($id);
        $chat->delete();
        return (new ChatResource($chat))->additional(['status' => 'success', 'message' => trans('dashboard.messages.success_delete')]);
    }

    public function messageIsSeen(Request $request,$id)
    {
        $chat = Chat::where(function($q){
            $q->where('sender_id',auth('api')->id())->orWhere('receiver_id',auth('api')->id());
        })->whereNull('read_at')->findOrFail($id);

        if ($chat) {
            $chat->update(['read_at' => now()]);
            $messages = $chat->messages()->whereNull('read_at')->where('receiver_id',auth('api')->id())->get();
            $chat->messages()->whereNull('read_at')->where('receiver_id',auth('api')->id())->update(['read_at' => now()]);
            broadcast(new MessageIsSeenEvent($chat,$messages));
            return response()->json(['status' => 'success', 'message' => trans('dashboard.messages.messages_is_seen'),'data' => null]);
        }
        return response()->json(['status' => 'success', 'message' => trans('dashboard.messages.msg_already_seen_or_you_not_have_chat'),'data' => null]);
    }

    public function getMessageObject($message)
    {
        return[
            'chat_id' => $message->chat_id,
            'sender_data' => ['id' => $message->sender_id,'fullname' => optional($message->sender)->fullname,'phone' => optional($message->sender)->phone,'image' => optional($message->sender)->avatar],
            'receiver_data' => ['id' => $message->receiver_id,'fullname' => optional($message->receiver)->fullname,'phone' => optional($message->receiver)->phone,'image' => optional($message->receiver)->avatar],
            'message_sender' => $message->sender_id,
            'message_id' => $message->id,
            'message' => $message->message,
            'message_position' => $message->sender_id == auth('api')->id() ? 'me' : 'other',
            'message_type' => $message->message_type,
            'created_at' => $message->created_at->format('Y-m-d H:i A'),
        ];
    }
}
