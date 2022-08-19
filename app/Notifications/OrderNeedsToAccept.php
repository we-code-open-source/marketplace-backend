<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\SmsChannel;
use App\Channels\WhatsappChannel;
use App\Models\Order;

class OrderNeedsToAccept extends Notification
{
    use Queueable;

    /**
     * @var Order
     */
    private $order;


    /**
     * Determine if notification will be send to restaurants users or not
     * @var bool
     */
    private $for_restaurants;


    /**
     * Create a new notification instance.
     * @param Order $order
     * @param bool $for_restaurants
     * @return void
     */
    public function __construct(Order $order, $for_restaurants = true)
    {
        $this->order = $order;
        $this->for_restaurants = $for_restaurants;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->for_restaurants) {
            $channels = [];
            if (setting('send_sms_notifications_for_restaurants', false)) {
                array_push($channels, SmsChannel::class);
            }
            if (setting('send_whatsapp_notifications_for_restaurants', false)) {
                array_push($channels, WhatsappChannel::class);
            }
            return $channels;
        } else {
            return [SmsChannel::class];
        }
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toSms($notifiable)
    {
        return "هناك طلبية برقم {$this->order->id} بحاجة للقبول";
    }

    /**
     * Get the whatsapp representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toWhatsapp($notifiable)
    {
        return "هناك طلبية برقم {$this->order->id} بحاجة للقبول";
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
            //
        ];
    }
}
