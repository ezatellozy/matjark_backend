<?php

namespace App\Notifications\Api\Dashboard\ReturnOrder;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Redis;

class ReturnOrderNotification extends Notification
{
    use Queueable;

    public $return_order;
    protected $via;
    private $title, $body;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($return_order, $via)
    {
        $this->return_order  = $return_order;
        $this->via    = $via;
        $this->title  = [
            'ar' => trans('dashboard.notifications.return_order.title.return_order', ['return_order_id' => $this->return_order->id], 'ar'),
            'en' => trans('dashboard.notifications.return_order.title.return_order', ['return_order_id' => $this->return_order->id], 'en'),
        ];
        $this->body = [
            'ar' => trans('dashboard.notifications.return_order.body.return_order', ['return_order_id' => $this->return_order->id], 'ar'),
            'en' => trans('dashboard.notifications.return_order.body.return_order', ['return_order_id' => $this->return_order->id], 'en'),
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
        if (in_array('fcm', $this->via)) {
            $this->toFcm($notifiable);
            if (($key = array_search('fcm', $this->via)) !== false) {
                unset($this->via[$key]);
            }
        }
        return $this->via;
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
                'notify_type'   => 'return_order',
                'notify_id'     => $notifiable->id,
                'notify_status' => '',
                'key'           => "return_order",
                'key_id'        => $this->return_order->id,
                'title'         => [
                    'en' => $this->title['en'],
                    'ar' => $this->title['ar'],
                ],
                'body'          => [
                    'en' => $this->body['en'],
                    'ar' => $this->body['ar']
                ],
                'sender_data'   => auth('api')->user()->only(['id', 'name', 'image']),
            ]
        ];

        Redis::publish('private-notification-outfit-product', json_encode($data));

        return [
            'key'    => "return_order",
            'key_id' => $this->return_order->id,
            // 'status' => $this->status,
            'title'  => $this->title,
            'body'   => $this->body,
        ];
    }

    public function toFcm($notifiable)
    {
        $data = [
            'key'    => "return_order",
            'key_id' => $this->return_order->id,
            // 'status' => $this->status,
            'title'  => $this->title[app()->getLocale()],
            'body'   => $this->body[app()->getLocale()],
        ];

        pushFluterFcmNotes($data, [$notifiable->id]);
    }
}
