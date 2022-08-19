<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\RestaurantChangedEvent' => [
            'App\Listeners\UpdateRestaurantEarningTableListener',
            'App\Listeners\ChangeClientRoleToManager',
        ],
        'App\Events\UserRoleChangedEvent' => [
            'App\Listeners\UpdateUserDriverTableListener',
        ],
        'App\Events\OrderChangedEvent' => [
            'App\Listeners\UpdateOrderEarningTable',
            'App\Listeners\UpdateOrderDriverTable'
        ],
        'App\Events\UpdatedOrderEvent' => [
            'App\Listeners\NotifyAvailableOrderListener',
            'App\Listeners\UpdateDriverStatusListener',
            'App\Listeners\UpdateOrderStatusInFirebaseListener',
            'App\Listeners\RemoveCanceledOrdersFromFirestoreListener',
            'App\Listeners\ReturnCouponsOfOrderListener',
        ],
        'App\Events\CreatedDriverEvent' => [
            'App\Listeners\UpdateDriverStatusInFirebaseListener',
        ],
        'App\Events\UpdatedDriverEvent' => [
            'App\Listeners\UpdateDriverStatusInFirebaseListener',
            'App\Listeners\UpdateDriverWorkTimeListener',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
