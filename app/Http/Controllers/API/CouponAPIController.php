<?php

namespace App\Http\Controllers\API;


use App\Criteria\Coupons\ValidCriteria;
use App\Models\Coupon;
use App\Repositories\CouponRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;

/**
 * Class CouponController
 * @package App\Http\Controllers\API
 */

class CouponAPIController extends Controller
{
    /** @var  CouponRepository */
    private $couponRepository;

    public function __construct(CouponRepository $couponRepo)
    {
        $this->couponRepository = $couponRepo;
    }

    /**
     * Display a listing of the Coupon.
     * GET|HEAD /coupons
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->couponRepository->pushCriteria(new RequestCriteria($request));
            $this->couponRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->couponRepository->pushCriteria(new ValidCriteria());
            if ($request->get('restaurant_id')) {
                $this->couponRepository->scopeQuery(function ($query) use ($request) {
                    return  $query->where('on_delivery_fee', true)->orWhere(function ($orQuery) use ($request) {
                        $params = getCriteriaSearchParams();
                        if (isset($params['code'])) {
                            $orQuery->where('code', $params['code']);
                        }
                        $orQuery->whereHas('discountables', function ($q) use ($request) {
                            $q->where("discountable_type", Restaurant::class)->where('discountable_id', $request->get('restaurant_id'));
                        });
                    });
                });
            }
            $coupons = $this->couponRepository->all();
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($coupons->toArray(), 'Coupons retrieved successfully');
    }

    /**
     * Display the specified Coupon.
     * GET|HEAD /coupons/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Coupon $coupon */
        if (!empty($this->couponRepository)) {
            $coupon = $this->couponRepository->findWithoutFail($id);
        }

        if (empty($coupon)) {
            return $this->sendError('Coupon not found');
        }

        return $this->sendResponse($coupon->toArray(), 'Coupon retrieved successfully');
    }
}
