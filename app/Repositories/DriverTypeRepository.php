<?php

namespace App\Repositories;

use App\Models\DriverType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DriverTypeRepository
 * @package App\Repositories
 * @version September 6, 2021, 10:37 am UTC
 *
 * @method DriverType findWithoutFail($id, $columns = ['*'])
 * @method DriverType find($id, $columns = ['*'])
 * @method DriverType first($columns = ['*'])
*/
class DriverTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'range',
        'last_access'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DriverType::class;
    }
}
