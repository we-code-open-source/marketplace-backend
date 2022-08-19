<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateOrderStatusInFirebaseListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $order = $event->order;
        if (!$order->wasChanged('order_status_id') || $order->created_at < now()->addHours('-3')) return;

        app('firebase.firestore')
            ->getFirestore()
            ->collection('current_orders')
            ->document($order->id)
            ->set([
                'id' => (string)$order->id,
                'order_status_id' => $order->order_status_id,
                'restaurant_id' => (string)$order->foodOrders[0]->food->restaurant_id,
                'user_id' => (string)$order->user_id,
                'driver_id' => (string)$order->driver_id,
                'total' => $order->payment->price,
                'created_at' => $order->created_at->timestamp,
            ]);
    }
}
