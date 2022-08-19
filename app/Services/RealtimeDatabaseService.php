<?php

namespace App\Services;

use App\Models\Distance;
use GuzzleHttp\Client;
use Log;
use Exception;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;



class RealtimeDatabaseService
{

    /**
     * The instance of the realtime database
     * 
     * @var Database
     */
    protected $database;


    public function __construct()
    {
        $serviceAccount = ServiceAccount::fromJsonFile(base_path(config('firebase.projects.app.credentials.file')));
        $this->database = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri(config('firebase.projects.app.database.url'))
            ->create()
            ->getDatabase();
    }

    public function getDatabase()
    {
        return $this->database;
    }
}
