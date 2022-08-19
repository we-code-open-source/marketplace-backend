<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Driver;

class UpdateDriverStatusListener
{

    private $order;
    private $driver;

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
        $this->order = $event->order;

        if (($this->order->wasRecentlyCreated && empty($this->order->driver_id)) || !$this->order->wasChanged(['driver_id', 'order_status_id']) || $this->orderNotAssignedToDriver()) {
            return; // exit if order new without driver , or order driver or status dose not changed , or order not assigned to driver
        }

        $this->driver = $this->order->driver->driver ?? false;
        $old_driver_id = $this->order->wasChanged('driver_id') ?  $this->order->getOriginal('driver_id') : false;

        if ($this->order->wasChanged('order_status_id')) {

            if ($this->order->isStatusWasWaittingDriver()) {
                // if order status was waitting driver and changed , then remove order from firestore to do not show it to drivers anymore
                app('firebase.firestore')->getFirestore()->collection('orders')->document($this->order->id)->delete();
            }

            if ($this->order->isStatusDone() || $this->order->isStatusCanceled()) {
                // when order is doen or canceled , that means driver will be set free 
                // so if driver changed , new driver his status will be default free and we do not need to update his status 
                // but we need to reset old driver status becuase he was linked to order and was busy
                $this->setDriverFree($old_driver_id ?  $this->getDriverById($old_driver_id) : null);
                return;
            }

            if (($this->order->isStatusWasDone() || $this->order->isStatusWasCanceled())) {
                // if order was done but returned active (not done or canceled becuase we checked it before this if statement), set driver busy 
                // even if driver changed we will update new driver and we will not care about old driver because he already free and we do not need to reset him free
                $this->setDriverBusy();
                return;
            }
        }

        // exit if order not new and driver_id not changed , so that menas you do not do anything
        //if (!$this->order->wasRecentlyCreated && !$this->order->isStatusDone() && !$this->order->isStatusCanceled() /* && !$this->order->wasChanged('driver_id') */) return;



        if (($this->order->wasRecentlyCreated || !$old_driver_id) &&  $this->order->driver_id) { //  assigned order to driver
            $this->setDriverBusy();
            return;
        }


        if ($old_driver_id &&  !$this->order->driver_id) { // driver canceled from order
            $this->setDriverFree($this->getDriverById($old_driver_id));
            return;
        }

        if ($old_driver_id && $this->order->driver_id) { // otherwise driver changed
            $this->driverChanged($old_driver_id);
        }
    }

    /**
     * Update driver working_on_order status to work (true)
     */
    protected function setDriverBusy($driver = null)
    {
        $this->updateDriverStatus(true, $driver);
    }


    /**
     * Update driver working_on_order status
     * Set old driver as free , and set new driver to busy
     * @param int $old_driver_id
     */
    protected function driverChanged($old_driver_id)
    {
        $this->updateDriverStatus(false, $this->getDriverById($old_driver_id));
        $this->updateDriverStatus(true);
    }


    /**
     * Set driver as free , that means dirver can see available orders
     */
    protected function setDriverFree($driver = null)
    {
        $this->updateDriverStatus(false, $driver);
    }

    /**
     * Update working_on_order property
     */
    private function updateDriverStatus($working_on_order, $driver = null)
    {
        if (!$driver) {
            $driver = $this->driver;
        }
        $driver->working_on_order = $working_on_order;
        $driver->save();
    }

    /**
     * Check if order not assigned to driver or not 
     * It check if order not assigned to driver before and after update
     */
    private function orderNotAssignedToDriver()
    {
        return !$this->order->wasChanged('driver_id') && !$this->order->driver_id;
    }

    private function getDriverById($id)
    {
        return Driver::where('user_id', $id)->first();
    }
}
