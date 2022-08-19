<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;
use Log;
use DB;

class CloseUnassignedOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Id of close unassigend orders operation , to use it in tracking in log system
     */
    protected $operationId;

    /**
     * Lifetime of orders before close if no drivers accept them
     * 
     * @var float
     */
    protected $lifetime_drivers;

    /**
     * Lifetime of orders before close if no restauarants accept them
     * 
     * @var int
     */
    protected $lifetime_restaurants;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->operationId = strtoupper(uniqid());
        $this->lifetime_drivers = (int)setting('order_expiration_time_before_accept_for_drivers');
        $this->lifetime_restaurants = (int)setting('order_expiration_time_before_accept_for_restaurants');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel('canceledOrders')->info("Started CloseUnassignedOrders Job : id => #$this->operationId");
        DB::transaction(function () {
            $drivers =  $this->cancelOrdersThatNotAssignedToDrivers()->count();
            $restaurants =  $this->cancelOrdersThatNotAcceptedFromRestaurant()->count();
            Log::channel('canceledOrders')->info("Ended CloseUnassignedOrders Job : id => #$this->operationId | drivers: $drivers | restaurants : $restaurants");
        });
    }

    /**
     * Cancel orders that took long time and did not assigned to drivers yet
     * 
     * @return App\Models\Order
     */
    public function cancelOrdersThatNotAssignedToDrivers()
    {
        $orders = Order::whereNull('driver_id')
            ->where('order_status_id', 10)
            ->where('created_at', '<=', now()->addSeconds($this->lifetime_drivers * -1))
            ->get();

        foreach ($orders as $order) {
            $old_status_id = $order->order_status_id;
            $order->order_status_id = 100; // canceled_no_drivers_available
            $order->save();
            $this->logOrderInfo($order, $old_status_id);
            $this->deleteOrderFromFirestore($order);
        }
        return $orders;
    }

    /**
     * Cancel order that took long time and restaurant did not accept them
     * 
     * @return App\Models\Order
     */
    public function cancelOrdersThatNotAcceptedFromRestaurant()
    {
        $orders = Order::where('order_status_id', 20)
            ->where('created_at', '<=', now()->addSeconds($this->lifetime_restaurants * -1))
            ->get();

        foreach ($orders as $order) {
            $old_status_id = $order->order_status_id;
            $order->order_status_id = 105; // canceled_restaurant_did_not_accept
            $order->save();
            $this->logOrderInfo($order, $old_status_id);
        }
        return $orders;
    }

    /**
     * Write information about canceled orders to log file
     * 
     * @param App\Models\Order $order
     * @param $old_status_id
     * 
     * @return void
     */
    protected function logOrderInfo($order, $old_status_id)
    {
        $data = $order->only('id', 'order_status_id', 'driver_id', 'created_at', 'updated_at');
        $data['old_order_status_id'] = $old_status_id;
        $data['operation_id'] = $this->operationId;
        $this->log(json_encode($data));
    }

    /**
     * Write information to canceled orders log file
     * 
     * @param App\Models\Order $order
     * @param $old_status_id
     * 
     * @return void
     */
    protected function log($data)
    {
        Log::channel('canceledOrdersDetails')->info($data);
    }

    /**
     * Delete order from firestore
     */
    protected function deleteOrderFromFirestore($order)
    {
        app('firebase.firestore')->getFirestore()->collection('orders')->document($order->id)->delete();
        $this->log(json_encode([
            'id' => $order->id,
            'status' => 'Deleted from firestore',
        ]));
    }
}
