<?php

namespace App\Notifications\Api\App\Cart;

use App\Http\Resources\Api\App\User\SenderResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CartItemNotification extends Notification
{
    use Queueable;
    public $cart;
    protected $via;
    private $title, $body;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($cart, $via)
    {
        $this->cart = $cart;
        $this->via = $via;
        $this->title = [
            'ar' => trans('app.orders.notifications.title.new_item_in_cart', ['cart_id' => $this->cart->id], 'ar'),
            'en' => trans('app.orders.notifications.title.new_item_in_cart', ['cart_id' => $this->cart->id], 'en'),
        ];
        $this->body = [
            'ar' => trans('app.orders.notifications.body.new_item_in_cart', ['cart_id' => $this->cart->id, 'client_name' => auth('api')->user()->fullname], 'ar'),
            'en' => trans('app.orders.notifications.body.new_item_in_cart', ['cart_id' => $this->cart->id, 'client_name' => auth('api')->user()->fullname], 'en'),
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
            'key' => "cart",
            'key_id' => $this->cart->id,
            'status' => "new_item",
            'title' => $this->title,
            'body' => $this->body,
            'sender_data' => new SenderResource(auth('api')->user()),
        ];
    }
    public function toFcm($notifiable)
    {
        $data = [
            'key' => "cart",
            'key_id' => $this->cart->id,
            'status' => "new_item",
            'title' => $this->title[app()->getLocale()],
            'body' => $this->body[app()->getLocale()],
            'sender_data' => new SenderResource(auth('api')->user()),
        ];
        pushFcm($data, [$notifiable->id]);
    }
}
