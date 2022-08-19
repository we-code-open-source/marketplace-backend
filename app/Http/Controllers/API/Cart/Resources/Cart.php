<?php

namespace App\Http\Controllers\API\Cart\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\API\Food\Resources\Food as FoodResource;

class Cart extends JsonResource
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
            ['food' => FoodResource::make($this->food)]
        );
    }
}
