<?php

namespace App\Http\Controllers\Api\Dashboard\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Notification\NotificationRequest;
use App\Http\Resources\Api\Dashboard\Notification\NotificationResource;
use App\Models\User;
use App\Notifications\Api\Dashboard\Management\ManagementNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
        // $admins = User::whereIn('user_type', ['admin', 'superadmin'])->pluck('id')->toArray();
        // $notifications = DatabaseNotification::whereHasMorph('notifiable', [User::class], function ($q) use ($admins, $request) {
        //     $q->whereIn('notifiable_id', $admins);
        //     if ($request->type == 'read') {

        //         $q->where('read_at', '!=', null);
        //     } elseif ($request->type == 'unread') {
        //         $q->where('read_at', null);
        //     }
        // })->latest()->paginate();

        $notifications = auth()->guard('api')->user()->notifications()->when($request->type != null, function($q) use($request){
            if ($request->type == 'read') {
                $q->where('read_at', '!=', null);
            } elseif ($request->type == 'unread') {
                $q->where('read_at', null);
            }
        })->latest()->paginate();

        $unread_count = auth()->guard('api')->user()->notifications()->where('read_at', null)->count();

        return NotificationResource::collection($notifications)->additional(['status' => 'success', 'message' => '','unread_count' => $unread_count]);
    }
    public function unreadNotificationCount()
    {
        $admins = User::whereIn('user_type', ['admin', 'superadmin'])->pluck('id')->toArray();
        // $count = DatabaseNotification::whereHasMorph('notifiable', [User::class], function ($q) use ($admins) {
        //     $q->whereIn('notifiable_id', $admins);
        //     $q->where('read_at', null);
        // })->count();


        $count = auth()->guard('api')->user()->notifications()->where('read_at', null)->count();

        return response()->json(['status' => 'success', 'data' => ['unread'=> $count], 'messages' => '']);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NotificationRequest $request)
    {
        switch ($request->all) {
            case true:
                $users = User::where('user_type', 'client')->get();
                break;
            case false:
                $users = User::whereIn('id', $request->user_ids)->where('user_type', 'client')->get();
                break;
        }

        // $pushFcmNotes = [
        //     'notify_type' => 'management',
        //     'title'       => $request->title,
        //     'body'        => $request->body,
        // ];

        // $database = [
        //     'sender'      => auth()->guard('api')->user()->toJson(),
        //     'notify_type' => 'management',
        //     'title'       => ['en' => $request->title, 'ar' => $request->title],
        //     'body'        => ['en' => $request->body, 'ar' => $request->body]
        // ];

        $database = [ 
            'key'    => "user",
            'key_id' => "",
            'status' => "",
            'title'  => ['en' => $request->title, 'ar' => $request->title],
            'body'   => ['en' => $request->body, 'ar' => $request->body]
        ];

        Notification::send($users, new ManagementNotification($database));
        
        // pushFluterFcmNotes($pushFcmNotes, $users->pluck('id')->toArray());

        return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.send.success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $superAdmins = User::whereIn('user_type', ['admin', 'superadmin'])->pluck('id');

        $notification = DatabaseNotification::whereHasMorph('notifiable', [User::class], function ($q) use ($superAdmins) {
            $q->whereIn('notifiable_id', $superAdmins);
        })->findOrFail($id);
        
        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }
        return NotificationResource::make($notification)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $superAdmins = User::whereIn('user_type', ['admin', 'superadmin'])->pluck('id');
        $notification = DatabaseNotification::whereHasMorph('notifiable', [User::class], function ($q) use ($superAdmins) {
            $q->whereIn('notifiable_id', $superAdmins);
        })->findOrFail($id);
        if ($notification->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.success')]);
        }
        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail'), 422]);
    }
}
