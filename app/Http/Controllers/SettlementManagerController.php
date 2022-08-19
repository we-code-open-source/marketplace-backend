<?php

namespace App\Http\Controllers;

use App\DataTables\SettlementManagerDataTable;
use App\DataTables\SettlementManagerAvailableDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateSettlementManagerRequest;
use App\Http\Requests\UpdateSettlementManagerRequest;
use App\Models\FoodOrder;
use App\Models\Order;
use App\Models\Restaurant;
use App\Repositories\SettlementManagerRepository;
use App\Repositories\CustomFieldRepository;

use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use DB;
use Illuminate\Validation\ValidationException;

class SettlementManagerController extends Controller
{
    /** @var  SettlementManagerRepository */
    private $settlementManagerRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;



    public function __construct(SettlementManagerRepository $settlementManagerRepo, CustomFieldRepository $customFieldRepo)
    {
        parent::__construct();
        $this->settlementManagerRepository = $settlementManagerRepo;
        $this->customFieldRepository = $customFieldRepo;
    }

    /**
     * Display a listing of the SettlementManager.
     *
     * @param SettlementManagerDataTable $settlementManagerDataTable
     * @return Response
     */
    public function index(SettlementManagerDataTable $settlementManagerDataTable)
    {
        return $settlementManagerDataTable->render('settlement_managers.index');
    }

    /**
     * Display a listing of the SettlementDriver available.
     *
     * @param SettlementManagerAvailableDataTable $settlementManagerDataTable
     * @return Response
     */
    public function indexAvailable(SettlementManagerAvailableDataTable $settlementManagerDataTable)
    {
        return $settlementManagerDataTable->render('settlement_managers.available.index');
    }

    /**
     * Show the form for creating a new SettlementManager.
     *
     * @return Response
     */
    public function create()
    {
        $hasCustomField = in_array($this->settlementManagerRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->settlementManagerRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('settlement_managers.create')->with('restaurants', $this->getRestaurants())->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created SettlementManager in storage.
     *
     * @param CreateSettlementManagerRequest $request
     *
     * @return Response
     */
    public function store(CreateSettlementManagerRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->settlementManagerRepository->model());
        try {
            $orders = $this->calculateOrders($request->restaurant_id);
            $input = array_merge($input, [
                'count' => $orders->count,
                'amount' => $orders->amount,
                'delivery_coupons_amount' => $orders->delivery_coupons->amount,
                'delivery_coupons_count' => $orders->delivery_coupons->count,
                'restaurant_coupons_amount' => $orders->restaurant_coupons->amount,
                'restaurant_coupons_count' => $orders->restaurant_coupons->count,
                'restaurant_coupons_on_company_amount' => $orders->restaurant_coupons_on_company->amount,
                'restaurant_coupons_on_company_count' => $orders->restaurant_coupons_on_company->count,
                'fee' => $orders->admin_commission,
                'creator_id' => auth()->user()->id
            ]);
            // start save data in database
            DB::beginTransaction();
            $settlementManager = $this->settlementManagerRepository->create($input);
            $settlementManager->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            $this->updateOrdersSettlement($request->restaurant_id, $settlementManager->id);
            DB::commit();
            Flash::success(__('lang.saved_successfully', ['operator' => __('lang.settlement_manager')]));
        } catch (ValidatorException $e) {
            DB::rollback();
            Flash::error($e->getMessage());
        }

        return redirect(route('settlementManagers.index'));
    }

    /**
     * Display the specified SettlementManager.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $settlementManager = $this->settlementManagerRepository->findWithoutFail($id);

        if (empty($settlementManager)) {
            Flash::error('Settlement Manager not found');

            return redirect(route('settlementManagers.index'));
        }

        return view('settlement_managers.show')->with('settlementManager', $settlementManager);
    }


    /**
     * Display the specified settlementManager .
     * Available settlement by restaurant id
     *
     * @param  int $restaurant_id
     *
     * @return Response
     */
    public function showAvailable($restaurant_id)
    {
        $settlementManager = new \stdClass;
        $restaurant = Restaurant::findOrFail($restaurant_id);
        $settlementManager->restaurant = $restaurant;


        $food = FoodOrder::join('foods', 'foods.id', 'food_orders.food_id')
            ->join('orders', 'orders.id', 'food_orders.order_id')
            ->select(
                DB::raw('food_orders.order_id order_id'),
                DB::raw("IFNULL(SUM(food_orders.quantity * food_orders.price),0) amount"),
            )
            ->where('foods.restaurant_id', $restaurant_id)
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->groupBy('order_id');


        $orders = Order::joinSub($food, 'foods', function ($join) {
            $join->on('orders.id', '=', 'foods.order_id');
        })->get();


        if (empty($settlementManager)) {
            Flash::error('Settlement Manager not found');

            return redirect(route('settlementManagers.indexAvailable'));
        }

        $settlementManager->orders = $orders;
        $settlementManager->count = $orders->count();
        $settlementManager->sales_amount = $orders->sum('amount');
        $settlementManager->amount = ($restaurant->admin_commission / 100) * $settlementManager->sales_amount;
        $settlementManager->fee = $restaurant->admin_commission;

        return view('settlement_managers.available.show')->with('settlementManager', $settlementManager);
    }


    /**
     * Print the specified SettlementDriver.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function print($id)
    {
        $settlementManager = $this->settlementManagerRepository->findWithoutFail($id);

        if (empty($settlementManager)) {
            Flash::error('Settlement Manager not found');

            return redirect(route('settlementManagers.index'));
        }

        $settlementManager->loadOrders();

        return view('settlement_managers.print')->with('settlement', $settlementManager);
    }


    /**
     * Show the form for editing the specified SettlementManager.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $settlementManager = $this->settlementManagerRepository->findWithoutFail($id);

        if (empty($settlementManager)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.settlement_manager')]));

            return redirect(route('settlementManagers.index'));
        }
        $customFieldsValues = $settlementManager->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->settlementManagerRepository->model());
        $hasCustomField = in_array($this->settlementManagerRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('settlement_managers.edit')
            ->with('restaurants', $this->getRestaurants())
            ->with('settlementManager', $settlementManager)
            ->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified SettlementManager in storage.
     *
     * @param  int              $id
     * @param UpdateSettlementManagerRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSettlementManagerRequest $request)
    {
        $settlementManager = $this->settlementManagerRepository->findWithoutFail($id);

        if (empty($settlementManager)) {
            Flash::error('Settlement Manager not found');
            return redirect(route('settlementManagers.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->settlementManagerRepository->model());

        try {
            $restaurant_changed = $request->restaurant_id != $settlementManager->restaurant_id;

            if ($restaurant_changed) { // when restaurant change , recalculate orders
                $orders = $this->calculateOrders($request->restaurant_id);
                $input = array_merge($input, [
                    'count' => $orders->count,
                    'amount' => $orders->amount,
                    'delivery_coupons_amount' => $orders->delivery_coupons->amount,
                    'delivery_coupons_count' => $orders->delivery_coupons->count,
                    'restaurant_coupons_amount' => $orders->restaurant_coupons->amount,
                    'restaurant_coupons_count' => $orders->restaurant_coupons->count,
                    'restaurant_coupons_on_company_amount' => $orders->restaurant_coupons_on_company->amount,
                    'restaurant_coupons_on_company_count' => $orders->restaurant_coupons_on_company->count,
                    'fee' => $orders->admin_commission,
                    'creator_id' => auth()->user()->id
                ]);
            }

            // start save data in database
            DB::beginTransaction();
            $settlementManager = $this->settlementManagerRepository->update($input, $id);

            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $settlementManager->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }

            if ($restaurant_changed) { // when restaurant change , reset orders for old restaurant
                // reset orders status 
                $settlementManager->orders()->update(['settlement_manager_id' => null]);

                $this->updateOrdersSettlement($request->restaurant_id, $settlementManager->id);
            }
            DB::commit();
            Flash::success(__('lang.updated_successfully', ['operator' => __('lang.settlement_manager')]));
        } catch (ValidatorException $e) {
            DB::rollback();
            Flash::error($e->getMessage());
        }

        return redirect(route('settlementManagers.index'));
    }

    /**
     * Remove the specified SettlementManager from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $settlementManager = $this->settlementManagerRepository->findWithoutFail($id);

        if (empty($settlementManager)) {
            Flash::error('Settlement Manager not found');

            return redirect(route('settlementManagers.index'));
        }

        try {
            $settlementManager->orders()->update(['settlement_manager_id' => null]);
            $this->settlementManagerRepository->delete($id);
            Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.settlement_manager')]));
        } catch (ValidatorException $e) {
            DB::rollback();
            Flash::error($e->getMessage());
        }


        return redirect(route('settlementManagers.index'));
    }

    /**
     * Remove Media of SettlementManager
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $settlementManager = $this->settlementManagerRepository->findWithoutFail($input['id']);
        try {
            if ($settlementManager->hasMedia($input['collection'])) {
                $settlementManager->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }



    /**
     * Get restaurants as array key => value
     * @return array 
     */
    private function getRestaurants()
    {
        return Restaurant::pluck('name', 'id')
            ->map(function ($v, $k) {
                return $v = "$k-$v";
            });
    }


    private function calculateOrders($restaurant_id)
    {
        $orders = FoodOrder::join('orders', 'orders.id', 'food_orders.order_id')
            ->select(
                DB::raw("IFNULL(SUM(food_orders.quantity * food_orders.price),0) amount"),
                DB::raw('IFNULL(COUNT(DISTINCT food_orders.order_id),0) count')
            )
            ->where('orders.restaurant_id', $restaurant_id)
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->first();

        if ($orders->count == 0) {
            throw ValidationException::withMessages(["There is no available orders for settelment"]);
        }

        $orders->restaurant_coupons = Order::join('coupons', 'coupons.id', 'orders.restaurant_coupon_id')
            //->join('discountables', 'discountables.coupon_id', 'coupons.id')
            ->select(
                DB::raw('IFNULL(SUM(orders.restaurant_coupon_value),0) amount'),
                DB::raw('IFNULL(COUNT(DISTINCT orders.id),0) count')
            )
            ->where('orders.restaurant_id', $restaurant_id)
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->where('coupons.cost_on_restaurant', true)
            //->where('discountables.discountable_id', $restaurant_id)
            //->where('discountables.discountable_type', Restaurant::class)
            ->first();


        $orders->restaurant_coupons_on_company = Order::join('coupons', 'coupons.id', 'orders.restaurant_coupon_id')
            //->join('discountables', 'discountables.coupon_id', 'coupons.id')
            ->select(
                DB::raw('IFNULL(SUM(orders.restaurant_coupon_value),0) amount'),
                DB::raw('IFNULL(COUNT(DISTINCT orders.id),0) count')
            )
            ->where('orders.restaurant_id', $restaurant_id)
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->where('coupons.cost_on_restaurant', false)
            //->where('discountables.discountable_id', $restaurant_id)
            //->where('discountables.discountable_type', Restaurant::class)
            ->first();

        $orders->delivery_coupons = Order::join('coupons', 'coupons.id', 'orders.delivery_coupon_id')
            ->select(
                DB::raw('IFNULL(SUM(orders.delivery_coupon_value),0) amount'),
                DB::raw('IFNULL(COUNT(DISTINCT orders.id),0) count')
            )
            ->where('orders.restaurant_id', $restaurant_id)
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->where('coupons.cost_on_restaurant', true)
            //->where('coupons.on_delivery_fee', true)
            ->first();

        $orders->admin_commission =  Restaurant::select('admin_commission')->where('id', $restaurant_id)->first()->admin_commission;

        $orders->amount  = ($orders->admin_commission / 100) *  ($orders->amount - $orders->restaurant_coupons->amount); // calculate amount;

        return $orders;
    }

    /**
     * Update orders settlement status 
     */
    private function updateOrdersSettlement($restaurant_id, $settlementId)
    {
        return Order::join('food_orders', 'food_orders.order_id', 'orders.id')
            ->join('foods', 'foods.id', 'food_orders.food_id')
            ->where('foods.restaurant_id', $restaurant_id)
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->update(['settlement_manager_id' => $settlementId]);
    }
}
