<?php

namespace App\Providers;

use App\Services\FirestoreService;
use App\Services\RealtimeDatabaseService;
use Illuminate\Support\ServiceProvider;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('firebase.firestore', function () {
            return new FirestoreService();
        });

        $this->app->singleton('firebase.realtime', function () {
            return new RealtimeDatabaseService();
        });
    }
}
