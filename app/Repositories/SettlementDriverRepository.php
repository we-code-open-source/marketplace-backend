<?php

namespace App\Repositories;

use App\Models\SettlementDriver;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SettlementDriverRepository
 * @package App\Repositories
 * @version September 17, 2021, 9:25 pm UTC
 *
 * @method SettlementDriver findWithoutFail($id, $columns = ['*'])
 * @method SettlementDriver find($id, $columns = ['*'])
 * @method SettlementDriver first($columns = ['*'])
 */
class SettlementDriverRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'driver_id',
        'amount',
        'note',
        'count',
        'creator_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SettlementDriver::class;
    }
}
