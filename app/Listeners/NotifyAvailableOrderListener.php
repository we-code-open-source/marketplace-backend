<?php

namespace App\Listeners;

use App\Services\AddOrderToFirebaseService;


class NotifyAvailableOrderListener
{
    protected $order;
    protected $restaurant;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->order = $event->order;
        $this->restaurant = $this->order->foodOrders[0]->food->restaurant;

        if ($this->order->driver_id || !$this->order->wasChanged(['order_status_id']) || $this->order->order_status_id != 30) { // 30 : accepted_from_restaurant
            return; // exit if order has driver or status dose not changed , or order not accepted_from_restaurant
        }

        if ($this->restaurant->private_drivers || $this->order->payment->isPayOnPickUp()) {
            return;
        }
        new AddOrderToFirebaseService($this->order);
    }
}
