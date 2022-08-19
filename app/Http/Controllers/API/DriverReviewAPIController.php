<?php

namespace App\Http\Controllers\API;

use App\Criteria\General\UserCriteria;
use App\Http\Requests\CreateDriverReviewRequest;
use App\Models\DriverReview;
use App\Repositories\DriverReviewRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class DriverReviewAPIController
 * @package App\Http\Controllers\API
 */

class DriverReviewAPIController extends Controller
{

    /** @var  DriverReviewRepository */
    private $driverReviewRepository;


    public function __construct(DriverReviewRepository $driverReviewRepo)
    {
        $this->driverReviewRepository = $driverReviewRepo;
    }

    /**
     * Display a listing of the DriverReview.
     * GET|HEAD /driverReviews
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->driverReviewRepository->pushCriteria(new RequestCriteria($request));
            $this->driverReviewRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->driverReviewRepository->pushCriteria(new UserCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $driverReviews = $this->driverReviewRepository->all();

        return $this->sendResponse($driverReviews->toArray(), 'Driver Reviews retrieved successfully');
    }

    /**
     * Display the specified DriverReview.
     * GET|HEAD /driverReviews/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var DriverReview $driverReview */
        if (!empty($this->driverReviewRepository)) {
            $this->driverReviewRepository->pushCriteria(new UserCriteria(request()));
            $driverReview = $this->driverReviewRepository->findWithoutFail($id);
        }

        if (empty($driverReview)) {
            return $this->sendError('Driver Review not found');
        }

        return $this->sendResponse($driverReview->toArray(), 'Driver Review retrieved successfully');
    }

    /**
     * Store a newly created DriverReview in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|integer|exists:users,id',
            'review' => 'required|string',
            'rate' => 'required|integer|min:1|max:5',
        ]);
        $uniqueInput = array_merge(
            ['user_id' => auth()->user()->id],
            $request->only("driver_id")
        );
        $otherInput = $request->except("user_id", "driver_id");
        try {
            $driverReview = $this->driverReviewRepository->updateOrCreate($uniqueInput, $otherInput);
        } catch (ValidatorException $e) {
            return $this->sendError('Driver Review not found');
        }

        return $this->sendResponse($driverReview->toArray(), __('lang.saved_successfully', ['operator' => __('lang.driver_review')]));
    }
}
