<?php

namespace App\Repositories;

use App\Models\SettlementManager;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SettlementManagerRepository
 * @package App\Repositories
 * @version September 25, 2021, 1:46 am UTC
 *
 * @method SettlementManager findWithoutFail($id, $columns = ['*'])
 * @method SettlementManager find($id, $columns = ['*'])
 * @method SettlementManager first($columns = ['*'])
*/
class SettlementManagerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'creator_id',
        'restaurant_id',
        'count',
        'amount',
        'note',
        'fee'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SettlementManager::class;
    }
}
