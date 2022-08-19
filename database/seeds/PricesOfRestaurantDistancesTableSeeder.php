<?php

use Illuminate\Database\Seeder;

class PricesOfRestaurantDistancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //I really should use factories like the last project but this is ok too
        $distance = new \App\Models\RestaurantDistancePrice();
        $distance->price = 0;
        $distance->from = 0;
        $distance->to = 5;
        $distance->restaurant_id = 3;
        $distance->save();

        $distance = new \App\Models\RestaurantDistancePrice();
        $distance->price = 10;
        $distance->from = 5;
        $distance->to = 10;
        $distance->restaurant_id = 3;
        $distance->save();

        $distance = new \App\Models\RestaurantDistancePrice();
        $distance->price = 20;
        $distance->from = 10;
        $distance->to = 20;
        $distance->restaurant_id = 3;
        $distance->save();

        $distance = new \App\Models\RestaurantDistancePrice();
        $distance->price = 5;
        $distance->from = 0;
        $distance->to = 5;
        $distance->restaurant_id = 7;
        $distance->save();
    }
}
