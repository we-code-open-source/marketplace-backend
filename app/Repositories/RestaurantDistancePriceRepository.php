<?php

namespace App\Repositories;

use App\Models\RestaurantDistancePrice;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class RestaurantDistancePriceRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;


    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RestaurantDistancePrice::class;
    }


}
