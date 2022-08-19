<?php

/**
 * File name: StatisticAPIController.php
 * Last modified: 2021.09.22 at 17:25:21
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UnregisteredCustomer;
use Illuminate\Http\Request;
use DB;

class UnregisteredCustomerAPIController extends Controller
{


    /**
     * Display the specified RestaurantsPayout.
     * GET|HEAD /restaurantsPayouts/{id}
     *
     * @param  string $phone
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($phone)
    {
        $obj =  UnregisteredCustomer::with('address')->where('phone', $phone)->latest()->first();

        if (empty($obj)) {
            return $this->sendError('Customer does not exist');
        }

        return $obj;
    }
}
