<?php

use Illuminate\Database\Seeder;

class OrderStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('order_statuses')->delete();

        \DB::table('order_statuses')->insert([
            [
                'id' => 1,
                'key' => 'order_received',
                'status' => 'Order Received',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'key' => 'waiting_for_drivers',
                'status' => 'Waiting for drivers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 20,
                'key' => 'waiting_for_restaurant',
                'status' => 'Waiting for restaurant',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 30,
                'key' => 'accepted_from_restaurant',
                'status' => 'Accepted from restaurant',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 40,
                'key' => 'driver_assigned',
                'status' => 'Driver assigned',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 50,
                'key' => 'driver_pick_up',
                'status' => 'Driver puck up',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 60,
                'key' => 'on_the_way',
                'status' => 'On the way',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 70,
                'key' => 'driver_arrived',
                'status' => 'Driver arrived',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 80,
                'key' => 'delivered',
                'status' => 'Order Delivered',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 100,
                'key' => 'canceled_no_drivers_available',
                'status' => 'No drivers available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 105,
                'key' => 'canceled_restaurant_did_not_accept',
                'status' => 'Restaurant did not accept',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 110,
                'key' => 'canceled_from_customer',
                'status' => 'Canceled from customer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 120,
                'key' => 'canceled_from_restaurant',
                'status' => 'Canceled from restaurant',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 130,
                'key' => 'canceled_from_driver',
                'status' => 'Canceled from driver',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 140,
                'key' => 'canceled_from_company',
                'status' => 'Canceled from company',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
