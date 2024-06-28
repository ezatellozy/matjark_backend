<?php

namespace App\Http\Controllers\Api\Website\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Website\Notification\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function make_notifications_is_read(Request $request)
    {

        $lang = app()->getLocale();

        auth()->guard('api')->user()->notifications()->update(['read_at' => now()]);

        return response(['status' => 'success', 'message' => $lang == 'en' ? 'notifications readed successfully' : 'notifications readed successfully', 'data' => null], 200);
    }

    
    public function index(Request $request)
    {
        $notifications = auth()->guard('api')->user()->notifications()->paginate(20);

        // auth()->guard('api')->user()->notifications()->update(['read_at' => now()]);

        $count_of_notification = auth()->guard('api')->user()->notifications()->where('read_at', null)->count();

        return NotificationResource::collection($notifications)->additional(['status' => 'success', 'message' => '', 'count_of_notification' => $count_of_notification]);
    }

    public function show($id)
    {
        $notification = auth()->guard('api')->user()->notifications()->findOrFail($id);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }
        return (new NotificationResource($notification))->additional(['status' => 'success', 'message' => '']);
    }

    public function destroy($id)
    {
        $notification = auth()->guard('api')->user()->notifications()->findOrFail($id);
        $notification->delete();
        return response()->json(['data' => null, 'status' => 'success', 'message' => trans('api.messages.deleted_successfully')]);
    }

    public function delete_all_notifications(Request $request)
    {
        auth()->guard('api')->user()->notifications()->delete();
        return response(['status' => 'success', 'message' => trans('api.messages.deleted_successfully'), 'data' => null], 200);
    }
}
