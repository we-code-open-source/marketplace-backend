<?php

namespace App\Services;


use Google\Cloud\Firestore\FirestoreClient;


class FirestoreService
{

    /**
     * The instance of the realtime database
     * 
     * @var Google\Cloud\Firestore\FirestoreClient
     */
    protected $firestore;


    public function __construct()
    {
        $this->firestore = new FirestoreClient([
            'projectId' => config('firebase.projects.app.project_id'),
        ]);
    }

    public function getFirestore()
    {
        return $this->firestore;
    }
}
