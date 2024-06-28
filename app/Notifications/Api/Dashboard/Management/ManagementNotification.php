<?php

namespace App\Notifications\Api\Dashboard\Management;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Redis;

class ManagementNotification extends Notification
{
    use Queueable;

    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
                'notify_type'   => 'management',
                'notify_id'     => $notifiable->id,
                'notify_status' => '',
                'key'           => "user",
                'key_id'        => $notifiable->id,
                'title'         => [
                    'en' => $this->data['title']['en'],
                    'ar' => $this->data['title']['ar'],
                ],
                'body'          => [
                    'en' => $this->data['body']['en'],
                    'ar' => $this->data['body']['ar'],
                ],
                'sender_data'   => auth('api')->user()->only(['id', 'name', 'image']),
            ]
        ];

        Redis::publish('private-notification-outfit-product', json_encode($data));

        return $this->data;
    }
}
