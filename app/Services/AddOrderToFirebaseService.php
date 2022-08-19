<?php

namespace App\Services;

use App\Notifications\AvailableOrder;
use Illuminate\Support\Facades\Notification;
use App\Models\DriverType;
use App\Models\Order;
use App\Models\User;

class AddOrderToFirebaseService
{
    protected $order;
    protected $restaurant;



    /**
     * Create the event listener.
     *
     * @param App\Models\Order $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->restaurant = $this->order->foodOrders[0]->food->restaurant;
        if ($this->restaurant->private_drivers || $this->order->payment->isPayOnPickUp()) {
            return;
        }

        $this->addOrderToFirebase();
    }


    /**
     * Get drivers who in order area range (distance that can driver deliver order)
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getDrivers()
    {
        $firestore = app('firebase.firestore')->getFirestore();

        $types = DriverType::select('id', 'last_access', 'range')->get();

        $drivers = $firestore->collection('drivers')
            ->orderBy("last_access", "desc")
            ->where('working_on_order', '=', false)
            ->where('available', '=', true)
            ->where('last_access', '>', now()->addSeconds($types->max('last_access') * -1)->timestamp)
            ->documents();


        $collection = collect();

        foreach ($drivers as $d) {
            $collection->push($d->data());
        }

        $restaurant_longitude = $this->restaurant->longitude;
        $restaurant_latitude = $this->restaurant->latitude;

        $near_drivers = $collection->map(function ($item) use ($restaurant_latitude, $restaurant_longitude) {
            $item['distance'] =  get_distance($item['latitude'], $item['longitude'],  $restaurant_latitude, $restaurant_longitude);
            $item['id'] = (string) $item['id']; // convert it to string becuase mobile app can not filter int 
            return $item;
        })
            ->filter(function ($item) use ($types) { // filter by last_access depends on driver type 
                $last_access = $types->where('id', $item['driver_type_id'])->first()['last_access'];
                return $item['last_access'] >  now()->addSeconds($last_access * -1)->timestamp;
            })
            ->filter(function ($item) use ($types) { // filter by range depends on driver type
                $range = $types->where('id', $item['driver_type_id'])->first()['range'] + 10;
                return $item['distance'] <= $range;
            })
            ->sortBy('distance')
            ->take(25)
            ->map(function ($item) use ($restaurant_latitude, $restaurant_longitude) {
                $item['real_distance'] = app('distance')->getDistanceByKM($item['latitude'], $item['longitude'],  $restaurant_latitude, $restaurant_longitude);
                return $item;
            })
            ->filter(function ($item) use ($types) { // filter by real_distance depends on driver type
                $range = $types->where('id', $item['driver_type_id'])->first()['range'];
                return $item['real_distance'] <= $range;
            })
            ->sortBy('real_distance');


        return $near_drivers;
    }

    /**
     * Add order to firebase 
     */
    protected function addOrderToFirebase()
    {
        $drivers = $this->getDrivers();

        if ($drivers->count() == 0) {
            $this->order->order_status_id = 100; // 100 :  no drivers available
            $this->order->save();
            return;
        }

        $drivers_ids = $drivers->pluck('id')->toArray();

        app('firebase.firestore')
            ->getFirestore()
            ->collection('orders')
            ->document($this->order->id)
            ->set([
                'id' => $this->order->id,
                'restaurant' => ['id' => $this->restaurant->id, 'name' => $this->restaurant->name],
                'created_at' => $this->order->created_at->timestamp,
                'drivers' => $drivers_ids
            ]);

        $this->order->order_status_id = 10; // waiting for drivers
        $this->order->driver_id = null;
        $this->order->save();

        $users = User::select('id', 'device_token')->whereNotNull('device_token')
            ->whereIn('id', $drivers_ids)
            ->get();

        Notification::send($users, new AvailableOrder($this->order));
    }
}
