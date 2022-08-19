<?php

/**
 * File name: StatisticAPIController.php
 * Last modified: 2021.09.22 at 17:25:21
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API\Manager;

use App\Http\Controllers\Controller;
use App\Models\SettlementManager;
use App\Models\FoodOrder;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use DB;

class StatisticAPIController extends Controller
{

    public function index(Request $request)
    {
        $restaurant = auth()->user()->restaurants()->first();

        if (!$restaurant) {
            return response()->json(["error" => "User not linked to any restauarnt"], 403);
        }

        $settlements = SettlementManager::select(
            DB::raw("IFNULL(SUM(amount),0) amount"),
            DB::raw("IFNULL(SUM(delivery_coupons_amount),0) delivery_coupons_amount"),
            DB::raw("IFNULL(SUM(restaurant_coupons_amount),0) restaurant_coupons_amount"),
            DB::raw("IFNULL(SUM(restaurant_coupons_on_company_amount),0) restaurant_coupons_on_company_amount"),
            DB::raw("IFNULL(SUM(amount / fee * 100),0) manager_fee"),
            DB::raw('IFNULL(SUM(count),0) count'),
            DB::raw('IFNULL(SUM(delivery_coupons_count),0) delivery_coupons_count'),
            DB::raw('IFNULL(SUM(restaurant_coupons_count),0) restaurant_coupons_count'),
            DB::raw('IFNULL(SUM(restaurant_coupons_on_company_count),0) restaurant_coupons_on_company_count'),
        )
            ->where('restaurant_id', $restaurant->id)
            ->first()
            ->makeHidden(['custom_fields'])
            ->toArray();

        $settlements['manager_fee'] = round($settlements['manager_fee'], 3);


        $availabel_orders_for_settlement = FoodOrder::join('orders', 'orders.id', 'food_orders.order_id')
            ->select(
                DB::raw("IFNULL(SUM(food_orders.quantity * food_orders.price),0) orders_amount"),
                DB::raw('IFNULL(COUNT(DISTINCT food_orders.order_id),0) count')
            )
            ->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->first();

        $availabel_orders_for_settlement['restaurant_coupons'] = Order::join('coupons', 'coupons.id', 'orders.restaurant_coupon_id')
            ->join('discountables', 'discountables.coupon_id', 'coupons.id')
            ->select(
                DB::raw('IFNULL(SUM(orders.restaurant_coupon_value),0) amount'),
                DB::raw('IFNULL(COUNT(DISTINCT orders.id),0) count')
            )
            ->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->where('coupons.cost_on_restaurant', true)
            ->where('discountables.discountable_id', $restaurant->id)
            ->where('discountables.discountable_type', Restaurant::class)
            ->first();

        $availabel_orders_for_settlement['restaurant_coupons_on_company'] = Order::join('coupons', 'coupons.id', 'orders.restaurant_coupon_id')
            ->join('discountables', 'discountables.coupon_id', 'coupons.id')
            ->select(
                DB::raw('IFNULL(SUM(orders.restaurant_coupon_value),0) amount'),
                DB::raw('IFNULL(COUNT(DISTINCT orders.id),0) count')
            )
            ->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->where('coupons.cost_on_restaurant', false)
            ->where('discountables.discountable_id', $restaurant->id)
            ->where('discountables.discountable_type', Restaurant::class)
            ->first();

        $availabel_orders_for_settlement['delivery_coupons'] = Order::join('coupons', 'coupons.id', 'orders.delivery_coupon_id')
            ->select(
                DB::raw('IFNULL(SUM(orders.delivery_coupon_value),0) amount'),
                DB::raw('IFNULL(COUNT(DISTINCT orders.id),0) count')
            )
            ->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->where('coupons.cost_on_restaurant', true)
            //->where('coupons.on_delivery_fee', true)
            ->first();



        $availabel_orders_for_settlement->amount  = round(($restaurant->admin_commission / 100) *  ($availabel_orders_for_settlement->order_amount - $availabel_orders_for_settlement->restaurant_coupons->amount), 3);
        $availabel_orders_for_settlement['total'] = $availabel_orders_for_settlement->amount
            + $availabel_orders_for_settlement->delivery_coupons->amount
            - $availabel_orders_for_settlement->restaurant_coupons_on_company->amount;

        $data =  [
            'settlements' => $settlements,
            'availabel_orders_for_settlement' => $availabel_orders_for_settlement,
        ];

        return $this->sendResponse($data, "Statistics retrieved successfully");
    }
}
