<?php

namespace App\Notifications\Api\App\Order;

use App\Http\Resources\Api\App\User\SenderResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Redis;

class ClientCancelNotification extends Notification  implements ShouldBroadcast
{
    use Queueable;
    public $order;
    protected $via;
    private $title, $body;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order, $via)
    {
        $this->order = $order;
        $this->via = $via;
        $this->title = [
            'ar' => trans('app.orders.notifications.title.cancel_order', ['order_id' => $this->order->id], 'ar'),
            'en' => trans('app.orders.notifications.title.cancel_order', ['order_id' => $this->order->id], 'en'),
        ];
        $this->body = [
            'ar' => trans('app.orders.notifications.body.cancel_order', ['order_id' => $this->order->id, 'client_name' => auth('api')->user()->fullname], 'ar'),
            'en' => trans('app.orders.notifications.body.cancel_order', ['order_id' => $this->order->id, 'client_name' => auth('api')->user()->fullname], 'en'),
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
           if (in_array('broadcast', $this->via)) {
            $this->toBroadcast($notifiable);
            if (($key = array_search('broadcast', $this->via)) !== false) {
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
    
            
    public function toBroadcast($notifiable)
    {

        return new BroadcastMessage([
            'key' => "order",
            'key_id' => $this->order->id,
            'status' => "client_cancel",
            'title' => $this->title[app()->getLocale()],
            'body' => $this->body[app()->getLocale()],
            'sender_data' => new SenderResource(auth('api')->user()),

        ]);
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
                'notify_type'   => 'order',
                'notify_id'     => $notifiable->id,
                'notify_status' => 'client_cancel',
                'key'           => "order",
                'key_id'        => $this->order->id,
                'title'         => [
                    'en' => $this->title['en'],
                    'ar' => $this->title['ar'],
                ],
                'body'          => [
                    'en' => $this->body['en'],
                    'ar' => $this->body['ar']
                ],
               'sender_data' => new SenderResource(auth()->guard('api')->user()),
            ]
        ];

        Redis::publish('private-notification-outfit-product', json_encode($data));

        return [
            'key' => "order",
            'key_id' => $this->order->id,
            'status' => "client_cancel",
            'title' => $this->title,
            'body' => $this->body,
            'sender_data' => new SenderResource(auth('api')->user()),
        ];
    }
    
    public function toFcm($notifiable)
    {
        $data = [
            'key' => "order",
            'key_id' => $this->order->id,
            'status' => "client_cancel",
            'title' => $this->title[app()->getLocale()],
            'body' => $this->body[app()->getLocale()],
            'sender_data' => new SenderResource(auth('api')->user()),
        ];
        pushFcm($data, [$notifiable->id]);
    }
}
