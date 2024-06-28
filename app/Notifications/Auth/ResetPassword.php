<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

       /**
        * Create a notification instance.
        *
        * @param  string  $token
        * @return void
        */
       public function __construct()
       {
           
       }

       /**
        * Get the notification's channels.
        *
        * @param  mixed  $notifiable
        * @return array|string
        */
       public function via($notifiable)
       {
           return ['mail'];
       }

       /**
        * Get the notification message.
        *
        * @param  mixed  $notifiable
        * @return \Illuminate\Notifications\MessageBuilder
        */
       public function toMail($notifiable)
       {
         return (new MailMessage)
                    ->subject('استعادة كلمة المرور')
                    ->view('dashboard.email.reset',compact('notifiable'));

       }
}
