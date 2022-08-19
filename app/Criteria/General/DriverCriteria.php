<?php

namespace App\Criteria\General;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DriverCriteria.
 *
 * @package namespace App\Criteria;
 */
class DriverCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (auth()->check()) {
            return $model->where('driver_id', auth()->user()->id);
        }
        return $model;
    }
}
