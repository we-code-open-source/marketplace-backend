<?php

namespace App\Services;

use App\Models\Distance;
use GuzzleHttp\Client;
use Log;
use Exception;

class DistanceService
{

    /**
     * url of google map api
     * 
     * @var string
     */
    private $apiUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json';


    /**
     * Get distance and duration between two locations (points)
     */
    public function getDistance($from_latitude, $from_longitude, $to_latitude, $to_longitude, $skip_exceptions = True)
    {
        $from_latitude = number_format((float)$from_latitude, 5, '.', '');
        $from_longitude = number_format((float)$from_longitude, 5, '.', '');
        $to_latitude = number_format((float)$to_latitude, 5, '.', '');
        $to_longitude = number_format((float)$to_longitude, 5, '.', '');

        if ($from_latitude ==  $to_latitude && $from_longitude ==  $to_longitude) {
            return [
                "distance" => ["text" => "0 م",   "value" => 0],
                "duration" => ["text" => "0 د", "value" => 0],
            ];
        }
        $distance = Distance::select('distance_value', 'duration_value', 'distance_text', 'duration_text')
            ->where('from_latitude', $from_latitude)
            ->where('from_longitude', $from_longitude)
            ->where('to_latitude', $to_latitude)
            ->where('to_longitude', $to_longitude)
            ->first();

        if ($distance) {
            return $distance->getDistance();
        }

        try {
            return $this->setNewDistance($from_latitude, $from_longitude, $to_latitude, $to_longitude)->getDistance();
        } catch (Exception $e) {
            if (!$skip_exceptions) {
                throw $e;
            }
            return Distance::make([
                'distance_text' => "Nan",
                'distance_value' => 1000000, // 1000KM
                'duration_text' => "Nan",
                'duration_value' => 604800, // 1 week
            ])->getDistance();
        }
    }

    /**
     * Get distance by Kilo meters(km) between two locations (points)
     */
    public function getDistanceByKM($from_latitude, $from_longitude, $to_latitude, $to_longitude)
    {
        return $this->getDistanceByMeters(...func_get_args()) / 1000;
    }

    /**
     * Get distance by Kilomaters(km) between two locations (points)
     */
    public function getDistanceByMeters($from_latitude, $from_longitude, $to_latitude, $to_longitude)
    {
        return $this->getDistance(...func_get_args())['distance']['value'];
    }


    public function fetchDistanceFromGoogleMap($from_latitude, $from_longitude, $to_latitude, $to_longitude)
    {
        $client = new Client();

        try {
            $response = $client->get($this->apiUrl, [
                'query' => [
                    'units'        => 'metric',
                    'origins'      => "$from_latitude, $from_longitude",
                    'destinations' => "$to_latitude, $to_longitude",
                    'key'          => config('google-map.api_key'),
                    'language'     => 'en',
                    'mode'         => 'driving',
                ],
            ]);

            if (200 === $response->getStatusCode()) {
                $responseData = json_decode($response->getBody()->getContents());

                if ($responseData->status == 'OK' &&  $responseData->rows[0]->elements[0]->status == 'OK') {
                    return $responseData;
                }

                Log::error($response->getBody());
                throw new Exception("Bad response from google when calculate duration");
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("Calculate distance : {$e->getMessage()}");
        }
    }


    protected function saveDistanceToDB($from_latitude, $from_longitude, $to_latitude, $to_longitude, $response)
    {
        $distance = $response->rows[0]->elements[0];
        return Distance::create([
            'from_latitude' => $from_latitude,
            'from_longitude' => $from_longitude,
            'to_latitude' => $to_latitude,
            'to_longitude' => $to_longitude,
            'distance_text' => $distance->distance->text,
            'distance_value' => $distance->distance->value,
            'duration_text' => $distance->duration->text,
            'duration_value' => $distance->duration->value,
            'response' => json_encode($response)
        ]);
    }

    protected function setNewDistance($from_latitude, $from_longitude, $to_latitude, $to_longitude)
    {
        $distance = $this->fetchDistanceFromGoogleMap($from_latitude, $from_longitude, $to_latitude, $to_longitude);
        return $this->saveDistanceToDB($from_latitude, $from_longitude, $to_latitude, $to_longitude, $distance);
    }
}
