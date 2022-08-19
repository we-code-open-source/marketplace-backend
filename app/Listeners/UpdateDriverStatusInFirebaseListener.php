<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateDriverStatusInFirebaseListener
{

    protected $driver;

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
        $this->driver = $event->driver;

        if (!$this->driver->wasRecentlyCreated && !$this->driver->wasChanged(['driver_type_id', 'working_on_order', 'available'])) {
            return; // exit of driver not new and working_on_order and available not changed , so we do not need to do anything
        }


        $ref = app('firebase.firestore')->getFirestore()->collection('drivers')->document($this->driver->user_id);
        $data = $ref->snapshot()->data();

        if (!$data) {
            $data = [
                'id' => $this->driver->user_id,
                'latitude' => 0,
                'longitude' => 0,
                'last_access' => null,
            ];
        }

        $data['name'] = $this->driver->user->name;
        $data['phone_number'] = $this->driver->user->phone_number;
        $data['driver_type_id'] = $this->driver->driver_type_id;
        $data['working_on_order'] = $this->driver->working_on_order;
        $data['available'] = $this->driver->available;

        $ref->set($data);
    }
}
