<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RemoveOldOrdersInFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $documents = app('firebase.firestore')
            ->getFirestore()
            ->collection('current_orders')
            ->orderBy("created_at", "asc")
            ->where('created_at', '<', now()->addHours('-3')->timestamp)
            ->limit(100)
            ->documents();

        foreach ($documents as $document) {
            $document->reference()->delete();
        }
    }
}
