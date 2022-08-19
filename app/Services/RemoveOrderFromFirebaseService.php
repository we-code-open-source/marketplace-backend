<?php

namespace App\Services;

use App\Models\Order;

class RemoveOrderFromFirebaseService
{

    protected $order;


    /**
     * Create the event listener.
     *
     * @param App\Models\Order $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->removeOrderFromFirebase();
    }


    /**
     * Remove order from firebase 
     * 
     */
    protected function removeOrderFromFirebase()
    {
        app('firebase.firestore')
            ->getFirestore()
            ->collection('orders')
            ->document($this->order->id)
            ->delete();
    }
}
