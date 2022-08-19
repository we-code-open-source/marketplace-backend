<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;

/**
 * Class DistanceAPIController
 * @package App\Http\Controllers\API
 */

class DistanceAPIController extends Controller
{


    /**
     * Display a listing of the Driver.
     * GET|HEAD /drivers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistanceBetweenTwoPoints(Request $request)
    {
        try {
            $request->validate([
                'from_longitude' => "required",
                'from_latitude' => "required",
                'to_longitude' => "required",
                'to_latitude' => "required",
            ]);
            $result =  app('distance')->getDistance(
                $request->from_latitude,
                $request->from_longitude,
                $request->to_latitude,
                $request->to_longitude
            );
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($result, 'Distance retrieved successfully');
    }
}
