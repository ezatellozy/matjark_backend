<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Notification\{NotificationResource , NotificationCollection};

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = auth()->guard('api')->user()->notifications()->paginate(10);
        $unreadnotifications = auth()->guard('api')->user()->unreadNotifications;
        foreach ($unreadnotifications as $notification) {
            if (isset($notification->data['notify_type']) && $notification->data['notify_type'] == 'management' && is_null($notification->read_at)) {
                $notification->markAsRead();
            }
        }
        return (new NotificationCollection($notifications))->additional(['status' => 'success','message'=>'']);
    }


    public function show($id)
    {
        $notification = auth()->guard('api')->user()->notifications()->findOrFail($id);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }
        return (new NotificationResource($notification))->additional(['status' => 'success','message'=>'']);
    }


    public function destroy($id)
    {
        $notification = auth()->guard('api')->user()->notifications()->findOrFail($id);
        $notification->delete();
        return (new NotificationResource($notification))->additional(['status' => 'success','message'=>'تم الحذف بنجاح']);
    }

}
