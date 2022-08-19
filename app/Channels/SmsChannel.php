<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class SmsChannel
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
        if (method_exists($notifiable, 'routeNotificationForSms')) {
            $phone = $notifiable->routeNotificationForSms($notifiable);
        } else {
            $phone = $notifiable->phone  ?? $notifiable->phone_number;
        }

        $message = $notification->toSms($notifiable);

        return send_sms($phone, $message);
    }
}
