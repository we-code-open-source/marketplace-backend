<?php

namespace App\Repositories;

use App\Models\DriverWorkTime;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DriverWorkTimeRepository
 * @package App\Repositories
 * @version February 28, 2022, 10:08 am EET
 *
 * @method DriverWorkTime findWithoutFail($id, $columns = ['*'])
 * @method DriverWorkTime find($id, $columns = ['*'])
 * @method DriverWorkTime first($columns = ['*'])
 */
class DriverWorkTimeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DriverWorkTime::class;
    }
}
