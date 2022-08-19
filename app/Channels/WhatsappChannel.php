<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class WhatsappChannel
{

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notifiable, 'routeNotificationForWhatsapp')) {
            $phone = $notifiable->routeNotificationForWhatsapp($notifiable);
        } else {
            $phone = $notifiable->phone  ?? $notifiable->phone_number;
        }

        $message = $notification->toWhatsapp($notifiable);

        return send_whatsapp_msg($phone, $message);
    }
}
