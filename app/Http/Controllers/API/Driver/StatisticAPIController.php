<?php

/**
 * File name: StatisticAPIController.php
 * Last modified: 2021.09.22 at 17:25:21
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Models\SettlementDriver;
use App\Models\Order;
use Illuminate\Http\Request;
use DB;

class StatisticAPIController extends Controller
{

    public function index(Request $request)
    {
        $user_id = auth()->user()->id;

        //$delivered_orders = Order::where('driver_id', $user_id)->where('order_status_id', 80)->count();
        $settlements = SettlementDriver::select(
            DB::raw("IFNULL(SUM(amount),0) amount"),
            DB::raw("IFNULL(SUM(amount / fee * 100),0) delivery_fee"),
            DB::raw('IFNULL(SUM(count),0) count'),
            DB::raw('IFNULL(SUM(amount_restaurant_coupons),0) amount_restaurant_coupons'),
            DB::raw('IFNULL(SUM(amount_delivery_coupons),0) amount_delivery_coupons'),
            DB::raw('IFNULL(SUM(count_delivery_coupons),0) count_delivery_coupons'),
            DB::raw('IFNULL(SUM(count_restaurant_coupons),0) count_restaurant_coupons'),
        )
            ->where('driver_id', $user_id)
            ->first()
            ->makeHidden(['custom_fields'])
            ->toArray();

        $settlements['delivery_fee'] = round($settlements['delivery_fee'], 3);

        $availabel_orders_for_settlement = Order::select(
            DB::raw("IFNULL(SUM(delivery_fee),0) delivery_fee"),
            DB::raw('IFNULL(COUNT(*),0) count'),
            DB::raw('IFNULL(SUM(restaurant_coupon_value),0) amount_restaurant_coupons'),
            DB::raw('IFNULL(SUM(delivery_coupon_value),0) amount_delivery_coupons'),
            DB::raw('IFNULL(COUNT(delivery_coupon_id),0) count_delivery_coupons'),
            DB::raw('IFNULL(COUNT(restaurant_coupon_id),0) count_restaurant_coupons'),
        )
            ->where('driver_id', $user_id)
            ->where('order_status_id', 80) // Order Delivered
            ->whereNull('settlement_driver_id')
            ->first()
            ->makeHidden(['custom_fields'])
            ->toArray();

        $availabel_orders_for_settlement['fee'] = round(getDriverFee() / 100 * $availabel_orders_for_settlement['delivery_fee'], 3);


        $data =  [
            //'delivered_orders' => $delivered_orders,
            'settlements' => $settlements,
            'availabel_orders_for_settlement' => $availabel_orders_for_settlement,
        ];

        return $this->sendResponse($data, "Statistics retrieved successfully");
    }
}
