<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateDriverWorkTimeListener
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

        if (!$this->driver->wasChanged(['available'])) {
            return; // exit if driver available not changed , so we do not need to do anything
        }

        if ($this->driver->available) { // when driver start working (available => true)
            $this->driver->driverWorkTime()->create([
                'from_time' => now(),
                'created_by_id' => auth()->user()->id,
            ]);
        } else { // when driver stop working (available => false)
            $this->driver->driverWorkTime()
                ->whereNull('to_time')
                ->update([
                    'to_time' => now(),
                    'updated_by_id' => auth()->user()->id,
                ]);
        }
    }
}
