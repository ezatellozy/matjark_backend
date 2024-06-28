<?php

namespace App\Http\Controllers\Api\Dashboard\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Contact\ContactReplyRequest;
use App\Http\Resources\Api\Dashboard\Contact\ContactResource;
use App\Models\Contact;
use App\Models\User;
use App\Notifications\Api\Dashboard\Management\ManagementNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::latest()->paginate();

        return ContactResource::collection($contacts)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contacts = Contact::findOrFail($id);
        $contacts->update(['read_at' => now()]);

        return ContactResource::make($contacts)->additional(['status' => 'success', 'message' => '']);
    }

    public function reply(ContactReplyRequest $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $user = User::find($contact->user_id);

        if (! $user and $request->send_type != 'email')
        {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.send.you_cant_reply_to_this_message')], 422);
        }

        try {
            $contact->replies()->create($request->safe()->only('reply') + ['sender_id' => auth('api')->id(), 'receiver_id' => $contact->user_id]);

            switch ($request->send_type) {
                case 'fcm':
                    $fcm_data = [
                        'key' => 'management',
                        'title'       => trans('dashboard.noti.reply_contact_msg'),
                        'body'        => $request->reply,
                    
                        
                    ];
                    pushFcmNotes($fcm_data, [$contact->user_id]);
                    $database = [
                        'key' => 'management',
                        'title'       => ['en' => trans('dashboard.noti.reply_contact_msg'), 'ar' => trans('dashboard.noti.reply_contact_msg')],
                        'body'        => ['en' => $request->reply, 'ar' => $request->reply],
                        'sender_data'      => auth()->guard('api')->user()->toJson(),
                    ];
                    // Notification::send([$contact->user], new ManagementNotification($database, ['database']));
                            Notification::send($contact->user, new ManagementNotification($database, ['database', 'fcm']));

                    break;
                case 'sms':
                    // if (setting('use_sms_service') == 'enable') {
                    // }
                        // send_sms($contact->user->phone_code . validateIfPhoneStartWithZero($contact->user->phone), $request->reply);
                    //  send_smsForReply( validateIfPhoneStartWithZero($contact->user->phone), $request->reply);

                    break;
                case 'email':
                    Mail::send([], [], function ($message) use ($contact, $request) {
                        $message->to($contact->email)->subject(trans('dashboard.noti.reply_contact_msg'))->setBody($request->reply, 'text/html');
                    });
                    break;
            }
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.send.success')]);
        } catch (Exception $e) {
            info($e);
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.send.fail')], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);

        if ($contact->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
