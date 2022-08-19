<?php

/**
 * File name: DriverTypeAPIController.php
 * Last modified: 2021.09.07 at 09:04:18
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 *
 */

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\DriverType;
use App\Repositories\DriverTypeRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class Driver TypeController
 * @package App\Http\Controllers\API
 */
class DriverTypeAPIController extends Controller
{
    /** @var  DriverTypeRepository */
    private $driverTypeRepository;

    public function __construct(DriverTypeRepository $driverTypeRepo)
    {
        $this->driverTypeRepository = $driverTypeRepo;
    }

    /**
     * Display a listing of the Driver Type.
     * GET|HEAD /driverTypes
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->driverTypeRepository->pushCriteria(new RequestCriteria($request));
            $this->driverTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $driverTypes = $this->driverTypeRepository->all();

        return $this->sendResponse($driverTypes->toArray(), 'Driver types retrieved successfully');
    }

    /**
     * Display the specified Driver Type.
     * GET|HEAD /driverTypes/{id}
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Driver Type $driverType */
        if (!empty($this->driverTypeRepository)) {
            $driverType = $this->driverTypeRepository->findWithoutFail($id);
        }

        if (empty($driverType)) {
            return $this->sendError('Driver Type not found');
        }

        return $this->sendResponse($driverType->toArray(), 'Driver Type retrieved successfully');
    }
}
