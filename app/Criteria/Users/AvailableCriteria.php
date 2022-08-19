<?php

/**
 * File name: AvailableCriteria.php
 * Last modified: 2020.05.09 at 14:02:59
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Criteria\Users;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AvailableCriteria.
 *
 * @package namespace App\Criteria\Users;
 */
class AvailableCriteria implements CriteriaInterface
{
    /**
     * driver id to skip on filter
     * @var int
     */
    protected $driver_id;

    /**
     * AvailableCriteria constructor.
     */
    public function __construct($id)
    {
        $this->driver_id = $id;
    }

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
        return $model->join('drivers', 'users.id', '=', 'drivers.user_id')
            ->where(function ($q) {
                $q->where('available', true)->where('working_on_order', false);
                if ($this->driver_id) {
                    $q->orWhere('user_id', $this->driver_id);
                }
                return $q;
            });
    }
}
