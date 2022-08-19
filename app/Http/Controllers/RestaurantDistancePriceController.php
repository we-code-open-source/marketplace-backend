<?php

namespace App\Http\Controllers;

use App\DataTables\RestaurantDistancePriceDataTable;
use App\Http\Requests\CreateRestaurantDistancePriceRequest;
use App\Http\Requests\UpdateRestaurantDistancePriceRequest;
use App\Repositories\RestaurantDistancePriceRepository;
use App\Models\RestaurantDistancePrice;
use App\Repositories\RestaurantRepository;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Prettus\Validator\Exceptions\ValidatorException;

class RestaurantDistancePriceController extends Controller
{
    private $restaurantDistancePriceRepository;
    private $restaurantRepository;

    public function __construct(RestaurantDistancePriceRepository $restaurantDistancePriceRepo,RestaurantRepository $restaurantRepository)
    {
        parent::__construct();
        $this->restaurantDistancePriceRepository = $restaurantDistancePriceRepo;
        $this->restaurantRepository = $restaurantRepository;
    }

    public function index(RestaurantDistancePriceDataTable $dataTable)
    {
        return $dataTable->render('restaurant_distance_prices.index');
    }

    public function create()
    {
        $restaurants = $this->restaurantRepository->pluck('name','id');

        return view('restaurant_distance_prices.create',[
            'restaurants' => $restaurants,
        ]);
    }

    public function store(CreateRestaurantDistancePriceRequest $request)
    {
        $input = $request->all();
        try {
            $restaurant_distance_price = $this->restaurantDistancePriceRepository->create($input);
        } catch(ValidatorException $e) {
            Flash::error($e->getMessage());
        }catch (QueryException $e) {
            Flash::error(__('lang.restaurant_distance_price_save_error'));
            return back();
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.restaurant_distance_price')]));

        return redirect(route('restaurantDistancePrices.index'));
    }

    public function edit($id)
    {
        $restaurantDistancePrice = RestaurantDistancePrice::find($id);
        $restaurants = $this->restaurantRepository->pluck('name','id');


        if (empty($restaurantDistancePrice)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.restaurant_distance_price')]));

            return redirect(route('restaurantDistancePrices.index'));
        }

        return view('restaurant_distance_prices.edit', [
            'restaurantDistancePrice' => $restaurantDistancePrice,
            'restaurants' => $restaurants,
        ]);
    }

    public function update(UpdateRestaurantDistancePriceRequest $request, $id)
    {
        $restaurantDistancePrice = RestaurantDistancePrice::find($id);

        if (empty($restaurantDistancePrice)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.restaurant_distance_price')]));
            return redirect(route('restaurantDistancePrices.index'));
        }
        $input = $request->all();
        try {
            $restaurantDistancePrice = $this->restaurantDistancePriceRepository->update($input, $id);
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        } catch(QueryException $e) {
            Flash::error(__('lang.restaurant_distance_price_save_error'));
            return back();
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.restaurant_distance_price')]));

        return redirect(route('restaurantDistancePrices.index'));
    }

    public function destroy($id)
    {
        $restaurantDistancePrice = RestaurantDistancePrice::find($id);

        if (empty($restaurantDistancePrice)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.restaurant_distance_price')]));
            return redirect(route('restaurantDistancePrices.index'));
        }

        $this->restaurantDistancePriceRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.restaurant_distance_price')]));

        return redirect(route('restaurantDistancePrices.index'));
    }
}
