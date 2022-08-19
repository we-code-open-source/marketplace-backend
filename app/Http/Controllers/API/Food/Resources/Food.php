<?php

namespace App\Http\Controllers\API\Food\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\API\Restaurant\Resources\Restaurant as RestaurantResource;

class Food extends JsonResource
{
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
            ['restaurant' =>    RestaurantResource::make($this->restaurant)],
        );
    }
}
