<?php

namespace App\Http\Controllers\Api\App\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\App\Notification\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = auth()->guard('api')->user()->notifications()->paginate(20);
        auth()->guard('api')->user()->notifications()->update(['read_at' => now()]);
        return NotificationResource::collection($notifications)->additional(['status' => 'success', 'message' => '']);
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
