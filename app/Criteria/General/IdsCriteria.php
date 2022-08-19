<?php

/**
 * File name: IdsCriteria.php
 * Last modified: 2021.09.04 at 17:10:56
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\General;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class IdsCriteria.
 *
 * @package namespace App\Criteria\Orders;
 */
class IdsCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    private $request;

    /**
     * IdsCriteria constructor.
     * @param array $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        if (!$this->request->has('ids')) {
            return $model;
        } else {
            $statuses = $this->request->get('ids');
            if (in_array('0', $statuses)) { // means all statuses
                return $model;
            }
            return $model->whereIn('id', $this->request->get('ids', []));
        }
    }
}
