<?php

namespace App\Notifications\Api\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Redis;

class NewUserNotification extends Notification
{
    use Queueable;

    public $user;
    protected $via;
    private $title, $body;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $via)
    {
        $this->user  = $user;
        $this->via    = $via;
        $this->title  = [
            'ar' => trans('dashboard.notifications.users.title.new_user', ['user_id' => $this->user->id], 'ar'),
            'en' => trans('dashboard.notifications.users.title.new_user', ['user_id' => $this->user->id], 'en'),
        ];
        $this->body = [
            'ar' => trans('dashboard.notifications.users.body.new_user', ['user_id' => $this->user->id], 'ar'),
            'en' => trans('dashboard.notifications.users.body.new_user', ['user_id' => $this->user->id], 'en'),
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $user = $this->user;
        if( $user->profile->is_allow_notification == 1){
        if (in_array('fcm', $this->via) && $user != null  ) {
            $this->toFcm($notifiable);
            if (($key = array_search('fcm', $this->via)) !== false) {
                unset($this->via[$key]);
            }
        }

        return $this->via;
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {

        $data = [
            'event' => 'notification:' . $notifiable->id,
            'data'  => [
                'notify_type'   => 'user',
                'notify_id'     => $notifiable->id,
                'notify_status' => '',
                'key'           => "user",
                'key_id'        => $this->user->id,
                'title'         => [
                    'en' => $this->title['en'],
                    'ar' => $this->title['ar'],
                ],
                'body'          => [
                    'en' => $this->body['en'],
                    'ar' => $this->body['ar']
                ],
                'sender_data'   => null,
            ]
        ];

        Redis::publish('private-notification-outfit-product', json_encode($data));

        return [
            'key'    => "user",
            'key_id' => $this->user->id,
            'status' => null,
            'title'  => $this->title,
            'body'   => $this->body,
        ];
    }

    public function toFcm($notifiable)
    {
        // $data = [
        //     'key'    => "user",
        //     'key_id' => $this->user->id,
        //     'status' => null,
        //     'title'  => $this->title[app()->getLocale()],
        //     'body'   => $this->body[app()->getLocale()],
        //     // 'notification' => [
        //     //     'title' => $this->title[app()->getLocale()],
        //     //     'body' => $this->body[app()->getLocale()],
        //     // ]
        // ];

        // $data2 = [
        //     'key'    => "user",
        //     'key_id' => $this->user->id,
        //     'status' => null,
        //     'title'  => $this->title,
        //     'body'   => $this->body,
        // ];

        // // pushFcm($data, [$notifiable->id]);
        // // pushFcmNotes($data, [$notifiable->id]);

        // Redis::publish('private-notification-outfit-product', json_encode($data2));


        // pushFluterFcmNotes($data, [$notifiable->id]);
    }
}
