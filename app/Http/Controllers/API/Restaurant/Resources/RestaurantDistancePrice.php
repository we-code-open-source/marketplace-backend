<?php

namespace App\Http\Controllers\API\Restaurant\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantDistancePrice extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
