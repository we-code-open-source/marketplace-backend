<?php

namespace App\Http\Controllers\API\Restaurant\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Restaurant extends JsonResource
{

    /**
     * To save cache distance value of getDistance method
     * @var array
     */
    private $_get_distance;


    /**
     * To save cache delivery_prices value of getDeliveryPrices method
     * @var array
     */
    private $_get_delivery_prices;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(
            parent::toArray($request),
            $this->getDistance(),
            $this->getDeliveryPrices()
        );
    }

    public function getDistance()
    {
        if (!$this->_get_distance) {
            if (request()->has(['myLon', 'myLat'])) {
                $this->_get_distance =  ['distance' =>  app('distance')->getDistance(request()->myLat, request()->myLon, $this->latitude, $this->longitude)];
            } else {
                $this->_get_distance = [];
            }
        }

        return $this->_get_distance;
    }


    private function getDeliveryPrices()
    {
        if (!$this->_get_delivery_prices) {
            $this->_get_delivery_prices =  [
                'delivery_fee' => $this->getDeliveryPriceByType($this->delivery_price_type),
            ];
        }
        return $this->_get_delivery_prices;
    }

    private function getDeliveryPriceByType($delivery_price_type)
    {
        switch ($delivery_price_type) {
            case "fixed":
                return $this->delivery_fee;
            case "flexible":
                return 'flexible';
            case "distance":
                return RestaurantDistancePrice::collection($this->distancesPrices);
            default:
                return "No delivery price? that is improbable";
        }
    }
}
