<?php

namespace App\Http\Controllers\API\Driver;

use App\Criteria\General\DriverCriteria;
use App\DataTables\SettlementDriverDataTable;
use App\Repositories\SettlementDriverRepository;
use App\Repositories\CustomFieldRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class SettlementDriverController extends Controller
{
    /** @var  SettlementDriverRepository */
    private $settlementDriverRepository;


    public function __construct(SettlementDriverRepository $settlementDriverRepo)
    {
        parent::__construct();
        $this->settlementDriverRepository = $settlementDriverRepo;
    }

    /**
     * Display a listing of the SettlementDriver.
     *
     * @param SettlementDriverDataTable $settlementDriverDataTable
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $this->settlementDriverRepository->pushCriteria(new RequestCriteria($request));
            $this->settlementDriverRepository->pushCriteria(new DriverCriteria());
            $this->settlementDriverRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $settlements = $this->settlementDriverRepository->all();

        return $this->sendResponse($settlements->toArray(), 'Settlements retrieved successfully');
    }



    /**
     * Display the specified SettlementDriver.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $this->settlementDriverRepository->pushCriteria(new DriverCriteria());
        $settlementDriver = $this->settlementDriverRepository->with('orders')->findWithoutFail($id);

        if (empty($settlementDriver)) {
            return $this->sendError('Settlement not found');
        }

        return $this->sendResponse($settlementDriver->toArray(), 'Settlement retrieved successfully');
    }
}
