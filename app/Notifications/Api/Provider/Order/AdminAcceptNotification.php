<?php

namespace App\Notifications\Api\Provider\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAcceptNotification extends Notification
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
    public function __construct($order, $via, )
    {
        $this->order = $order;
        $this->via   = $via;
        $this->title = [
            'ar' => trans('dashboard.orders.notifications.title.accept_order', ['order_id' => $this->order->id], 'ar'),
            'en' => trans('dashboard.orders.notifications.title.accept_order', ['order_id' => $this->order->id], 'en'),
        ];
        $this->body = [
            'ar' => trans('dashboard.orders.notifications.body.accept_order', ['order_id' => $this->order->id], 'ar'),
            'en' => trans('dashboard.orders.notifications.body.accept_order', ['order_id' => $this->order->id], 'en'),
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
        return [
            'key'    => "order",
            'key_id' => $this->order->id,
            'status' => "admin_accept",
            'title'  => $this->title,
            'body'   => $this->body,
        ];
    }

    public function toFcm($notifiable)
    {
        $data = [
            'key'    => "order",
            'key_id' => $this->order->id,
            'status' => "admin_accept",
            'title'  => $this->title[app()->getLocale()],
            'body'   => $this->body[app()->getLocale()],
        ];

        pushFluterFcmNotes($data, [$notifiable->id]);
    }
}
