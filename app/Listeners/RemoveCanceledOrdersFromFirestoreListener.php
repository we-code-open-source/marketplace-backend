<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\RemoveOrderFromFirebaseService;

class RemoveCanceledOrdersFromFirestoreListener
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
        if ($order->wasChanged('order_status_id') && $order->order_status_id >= 100) {
            new RemoveOrderFromFirebaseService($order);
        }
    }
}
