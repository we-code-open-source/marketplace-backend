<?php

namespace App\Http\Controllers;

use App\DataTables\SettlementDriverDataTable;
use App\DataTables\SettlementDriverAvailableDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateSettlementDriverRequest;
use App\Http\Requests\UpdateSettlementDriverRequest;
use App\Repositories\SettlementDriverRepository;
use App\Repositories\CustomFieldRepository;

use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Criteria\Users\DriversCriteria;
use App\Models\Order;
use App\Repositories\UserRepository;
use DB;
use Illuminate\Validation\ValidationException;

class SettlementDriverController extends Controller
{
    /** @var  SettlementDriverRepository */
    private $settlementDriverRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;



    public function __construct(SettlementDriverRepository $settlementDriverRepo, UserRepository $userRepo, CustomFieldRepository $customFieldRepo)
    {
        parent::__construct();
        $this->settlementDriverRepository = $settlementDriverRepo;
        $this->userRepository = $userRepo;
        $this->customFieldRepository = $customFieldRepo;
    }

    /**
     * Display a listing of the SettlementDriver.
     *
     * @param SettlementDriverDataTable $settlementDriverDataTable
     * @return Response
     */
    public function index(SettlementDriverDataTable $settlementDriverDataTable)
    {
        return $settlementDriverDataTable->render('settlement_drivers.index');
    }

    /**
     * Display a listing of the SettlementDriver available.
     *
     * @param SettlementDriverAvailableDataTable $settlementDriverDataTable
     * @return Response
     */
    public function indexAvailable(SettlementDriverAvailableDataTable $settlementDriverDataTable)
    {
        return $settlementDriverDataTable->render('settlement_drivers.available.index');
    }

    /**
     * Show the form for creating a new SettlementDriver.
     *
     * @return Response
     */
    public function create()
    {
        $hasCustomField = in_array($this->settlementDriverRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->settlementDriverRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('settlement_drivers.create')->with("drivers", $this->getDrivers())->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created SettlementDriver in storage.
     *
     * @param CreateSettlementDriverRequest $request
     *
     * @return Response
     */
    public function store(CreateSettlementDriverRequest $request)
    {
        $input = $request->all();

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->settlementDriverRepository->model());
        try {
            $orders = $this->calculateOrders($request->driver_id);
            $input = array_merge($input, [
                'count_delivery_coupons' => $orders->count_delivery,
                'count_restaurant_coupons' => $orders->count_restaurant,
                'amount_delivery_coupons' => $orders->amount_delivery,
                'amount_restaurant_coupons' => $orders->amount_restaurant,
                'count' => $orders->count,
                'fee' => $orders->fee,
                'amount' => $orders->amount,
                'creator_id' => auth()->user()->id
            ]);
            // start save data in database
            DB::beginTransaction();
            $settlementDriver = $this->settlementDriverRepository->create($input);
            $settlementDriver->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            $this->updateOrdersSettlement($request->driver_id, $settlementDriver->id);
            DB::commit();
        } catch (ValidatorException $e) {
            DB::rollback();
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.settlement_driver')]));

        return redirect(route('settlementDrivers.index'));
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
        $settlementDriver = $this->settlementDriverRepository->findWithoutFail($id);

        if (empty($settlementDriver)) {
            Flash::error('Settlement Driver not found');

            return redirect(route('settlementDrivers.index'));
        }

        return view('settlement_drivers.show')->with('settlementDriver', $settlementDriver);
    }

    /**
     * Display the specified SettlementDriver .
     * Available settlement by driver id
     *
     * @param  int $driver_id
     *
     * @return Response
     */
    public function showAvailable($driver_id)
    {
        $settlementDriver = new \stdClass;

        $orders = Order::with('payment', 'driver:id,name')
            ->where('order_status_id', 80) // Order Delivered
            ->whereNull('settlement_driver_id')
            ->where("driver_id", $driver_id)
            ->get();


        if (empty($orders)) {
            Flash::error('Settlement Driver not found');

            return redirect(route('settlementDrivers.indexAvailable'));
        }

        $settlementDriver->orders = $orders;
        $settlementDriver->driver = $orders->first()->driver;
        $settlementDriver->count = $orders->count();
        $settlementDriver->delivery_fee = $orders->sum('delivery_fee');
        $settlementDriver->amount = (getDriverFee() / 100) * $settlementDriver->delivery_fee;
        $settlementDriver->fee = getDriverFee();

        return view('settlement_drivers.available.show')->with('settlementDriver', $settlementDriver);
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
        $settlementDriver = $this->settlementDriverRepository->findWithoutFail($id);

        if (empty($settlementDriver)) {
            Flash::error('Settlement Driver not found');

            return redirect(route('settlementDrivers.index'));
        }

        $settlementDriver->load('orders', 'orders.payment');

        return view('settlement_drivers.print')->with('settlement', $settlementDriver);
    }

    /**
     * Show the form for editing the specified SettlementDriver.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $settlementDriver = $this->settlementDriverRepository->findWithoutFail($id);

        if (empty($settlementDriver)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.settlement_driver')]));

            return redirect(route('settlementDrivers.index'));
        }
        $customFieldsValues = $settlementDriver->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->settlementDriverRepository->model());
        $hasCustomField = in_array($this->settlementDriverRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('settlement_drivers.edit')->with("drivers", $this->getDrivers())->with('settlementDriver', $settlementDriver)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified SettlementDriver in storage.
     *
     * @param  int              $id
     * @param UpdateSettlementDriverRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSettlementDriverRequest $request)
    {
        $settlementDriver = $this->settlementDriverRepository->findWithoutFail($id);

        if (empty($settlementDriver)) {
            Flash::error('Settlement Driver not found');
            return redirect(route('settlementDrivers.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->settlementDriverRepository->model());
        try {
            $driver_changed = $request->driver_id != $settlementDriver->driver_id;

            if ($driver_changed) { // when driver change , recalculate orders
                $orders = $this->calculateOrders($request->driver_id);
                $input = array_merge($input, [
                    'count_delivery_coupons' => $orders->count_delivery,
                    'count_restaurant_coupons' => $orders->count_restaurant,
                    'amount_delivery_coupons' => $orders->amount_delivery,
                    'amount_restaurant_coupons' => $orders->amount_restaurant,
                    'count' => $orders->count,
                    'fee' => $orders->fee,
                    'amount' => $orders->amount,
                ]);
            }

            // start save data in database
            DB::beginTransaction();
            $settlementDriver = $this->settlementDriverRepository->update($input, $id);

            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $settlementDriver->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }

            if ($driver_changed) { // when driver change , reset orders for old driver
                // reset orders status 
                Order::where('settlement_driver_id', $settlementDriver->id)
                    ->update(['settlement_driver_id' => null]);

                $this->updateOrdersSettlement($request->driver_id, $settlementDriver->id);
            }
            DB::commit();
        } catch (ValidatorException $e) {
            DB::rollback();
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.settlement_driver')]));

        return redirect(route('settlementDrivers.index'));
    }

    /**
     * Remove the specified SettlementDriver from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $settlementDriver = $this->settlementDriverRepository->findWithoutFail($id);

        if (empty($settlementDriver)) {
            Flash::error('Settlement Driver not found');

            return redirect(route('settlementDrivers.index'));
        }

        $settlementDriver->orders()->update(['settlement_driver_id' => null]); // reset order 
        $this->settlementDriverRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.settlement_driver')]));

        return redirect(route('settlementDrivers.index'));
    }

    /**
     * Remove Media of SettlementDriver
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $settlementDriver = $this->settlementDriverRepository->findWithoutFail($input['id']);
        try {
            if ($settlementDriver->hasMedia($input['collection'])) {
                $settlementDriver->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }



    private function getDrivers()
    {
        return $this->userRepository
            ->getByCriteria(new DriversCriteria())
            ->pluck('name', 'id')
            ->map(function ($v, $k) {
                return $v = "$k-$v";
            });
    }


    private function calculateOrders($driver_id)
    {
        $orders = Order::select(
            DB::raw("IFNULL(SUM(delivery_fee),0) amount"),
            DB::raw('IFNULL(COUNT(*),0) count'),
            DB::raw("IFNULL(SUM(delivery_coupon_value),0) amount_delivery"),
            DB::raw('IFNULL(SUM(restaurant_coupon_value),0) amount_restaurant'),
            DB::raw('IFNULL(COUNT(delivery_coupon_id),0) count_delivery'),
            DB::raw('IFNULL(COUNT(restaurant_coupon_id),0) count_restaurant'),
        )
            ->where('driver_id', $driver_id)
            ->where('order_status_id', 80) // Order Delivered
            ->whereNull('settlement_driver_id')
            ->first();
        if ($orders->count == 0) {
            throw ValidationException::withMessages(["There is no available orders for settelment"]);
        }

        $orders->fee = getDriverFee();
        $orders->amount  = ($orders->fee / 100) *  $orders->amount; // calculate amount;
        //$couponStttlement = $orders->amount_delivery + $orders->amount_restaurant;
        //$orders->amount  = $orders->amount + $couponStttlement;

        return $orders;
    }

    private function updateOrdersSettlement($driver_id, $settlementId)
    {
        return Order::where('driver_id', $driver_id)
            ->where('order_status_id', 80) // Order Delivered
            ->whereNull('settlement_driver_id')
            ->update(['settlement_driver_id' => $settlementId]);
    }
}
