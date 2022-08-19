<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * This listener return coupons count_used depends on order status
 * when order canceled it subtract one (-1) from coupons count_used
 * when order was canceled but after it returned as uncanceled , it addition one (+1) to coupons count_used
 */
class ReturnCouponsOfOrderListener
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
        if (!$order->wasChanged('order_status_id')) {
            return;
        }

        $value = 0;
        if ($order->getOriginal('order_status_id') < 100 && $order->order_status_id > 100) {
            $value = -1;
        }
        if ($order->getOriginal('order_status_id') > 100 && $order->order_status_id < 100) {
            $value = 1;
        }

        if ($order->delivery_coupon_id) {
            $order->deliveryCoupon->count_used += $value;
            $order->deliveryCoupon->save();
        }
        if ($order->restaurant_coupon_id) {
            $order->restaurantCoupon->count_used += $value;
            $order->restaurantCoupon->save();
        }
    }
}
