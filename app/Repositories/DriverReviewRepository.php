<?php

namespace App\Repositories;

use App\Models\DriverReview;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DriverReviewRepository
 * @package App\Repositories
 * @version October 1, 2021, 2:26 pm UTC
 *
 * @method DriverReview findWithoutFail($id, $columns = ['*'])
 * @method DriverReview find($id, $columns = ['*'])
 * @method DriverReview first($columns = ['*'])
*/
class DriverReviewRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'review',
        'rate',
        'user_id',
        'driver_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DriverReview::class;
    }
}
