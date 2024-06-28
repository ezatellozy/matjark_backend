<?php

namespace App\Notifications\Api\Provider\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminChangeStatusNotification extends Notification
{
    use Queueable;

    public $order;
    protected $via;
    private $status, $title, $body;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order, $via, $status)
    {
        $this->order  = $order;
        $this->via    = $via;
        $this->status = $status;
        $this->title  = [
            'ar' => trans('dashboard.notifications.orders.title.' . $status, ['order_id' => $this->order->id], 'ar'),
            'en' => trans('dashboard.notifications.orders.title.' . $status, ['order_id' => $this->order->id], 'en'),
        ];
        $this->body = [
            'ar' => trans('dashboard.notifications.orders.body.' . $status, ['order_id' => $this->order->id], 'ar'),
            'en' => trans('dashboard.notifications.orders.body.' . $status, ['order_id' => $this->order->id], 'en'),
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
        $user = $this->order->client;
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
        return [
            'key'    => "order",
            'key_id' => $this->order->id,
            'status' => $this->status,
            'title'  => $this->title,
            'body'   => $this->body,
        ];
    }

    public function toFcm($notifiable)
    {
        $data = [
            'key'    => "order",
            'key_id' => $this->order->id,
            'status' => $this->status,
            'title'  => $this->title[app()->getLocale()],
            'body'   => $this->body[app()->getLocale()],
        ];
        pushFcm($data, [$notifiable->id]);

        // pushFluterFcmNotes($data, [$notifiable->id]);
    }
}
