<?php

namespace App\Http\Controllers;

use App\Criteria\Users\DriversCriteria;
use App\DataTables\DriverWorkTimeDataTable;
use App\Http\Requests\CreateDriverWorkTimeRequest;
use App\Http\Requests\UpdateDriverWorkTimeRequest;
use App\Repositories\DriverWorkTimeRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class DriverWorkTimeController extends Controller
{
    /** @var  DriverWorkTimeRepository */
    private $driverWorkTimeRepository;



    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;


    public function __construct(DriverWorkTimeRepository $driverWorkTimeRepo, UserRepository $userRepo, CustomFieldRepository $customFieldRepo)
    {
        parent::__construct();
        $this->driverWorkTimeRepository = $driverWorkTimeRepo;
        $this->userRepository = $userRepo;
        $this->customFieldRepository = $customFieldRepo;
    }



    /**
     * Display a Statistics of the drivers working time.
     *
     * @param Request $request
     * @return Response
     */
    public function statistics(Request $request)
    {
        $drivers = $this->userRepository->getByCriteria(new DriversCriteria())->pluck('name', 'id')->prepend(null, "");

        $driverWorkTimes = collect();
        if ($request->driver) {
            // set default value for dates if no values set
            if (!$request->from_date) {
                $request->merge([
                    "from_date" => now()->format('Y-m-d'),
                ]);
            }
            if (!$request->to_date) {
                $request->merge([
                    "to_date" => now()->format('Y-m-d'),
                ]);
            }

            $this->driverWorkTimeRepository->scopeQuery(function ($query) use ($request) {
                $query->where('user_id', $request->driver);
                $query->whereDate('from_time', '>=', $request->input('from_date', now()->format('Y-m-d')));
                $query->whereDate('to_time', '<=', $request->input('to_date', now()->format('Y-m-d')));
                return $query;
            })
                ->with('user:id,name')
                ->select('user_id', 'from_time', 'to_time');
            $driverWorkTimes = $this->driverWorkTimeRepository->all();
        }

        return view(
            'driver_work_times.statistics',
            [
                'drivers' => $drivers,
                'driverWorkTimes' =>  $driverWorkTimes,
            ]
        );
    }


}
