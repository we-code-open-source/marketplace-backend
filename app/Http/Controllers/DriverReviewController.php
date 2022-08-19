<?php

namespace App\Http\Controllers;

use App\DataTables\DriverReviewDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateDriverReviewRequest;
use App\Http\Requests\UpdateDriverReviewRequest;
use App\Repositories\DriverReviewRepository;
use App\Repositories\CustomFieldRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class DriverReviewController extends Controller
{
    /** @var  DriverReviewRepository */
    private $driverReviewRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;



    public function __construct(DriverReviewRepository $driverReviewRepo, CustomFieldRepository $customFieldRepo)
    {
        parent::__construct();
        $this->driverReviewRepository = $driverReviewRepo;
        $this->customFieldRepository = $customFieldRepo;
    }

    /**
     * Display a listing of the DriverReview.
     *
     * @param DriverReviewDataTable $driverReviewDataTable
     * @return Response
     */
    public function index(DriverReviewDataTable $driverReviewDataTable)
    {
        return $driverReviewDataTable->render('driver_reviews.index');
    }

    /**
     * Show the form for creating a new DriverReview.
     *
     * @return Response
     */
    public function create()
    {
        $hasCustomField = in_array($this->driverReviewRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->driverReviewRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('driver_reviews.create')->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created DriverReview in storage.
     *
     * @param CreateDriverReviewRequest $request
     *
     * @return Response
     */
    public function store(CreateDriverReviewRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->driverReviewRepository->model());
        try {
            $driverReview = $this->driverReviewRepository->create($input);
            $driverReview->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.driver_review')]));

        return redirect(route('driverReviews.index'));
    }

    /**
     * Display the specified DriverReview.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $driverReview = $this->driverReviewRepository->findWithoutFail($id);

        if (empty($driverReview)) {
            Flash::error('Driver Review not found');

            return redirect(route('driverReviews.index'));
        }

        return view('driver_reviews.show')->with('driverReview', $driverReview);
    }

    /**
     * Show the form for editing the specified DriverReview.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $driverReview = $this->driverReviewRepository->findWithoutFail($id);

        if (empty($driverReview)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.driver_review')]));

            return redirect(route('driverReviews.index'));
        }
        $customFieldsValues = $driverReview->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->driverReviewRepository->model());
        $hasCustomField = in_array($this->driverReviewRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('driver_reviews.edit')->with('driverReview', $driverReview)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified DriverReview in storage.
     *
     * @param  int              $id
     * @param UpdateDriverReviewRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDriverReviewRequest $request)
    {
        $driverReview = $this->driverReviewRepository->findWithoutFail($id);

        if (empty($driverReview)) {
            Flash::error('Driver Review not found');
            return redirect(route('driverReviews.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->driverReviewRepository->model());
        try {
            $driverReview = $this->driverReviewRepository->update($input, $id);


            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $driverReview->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.driver_review')]));

        return redirect(route('driverReviews.index'));
    }

    /**
     * Remove the specified DriverReview from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $driverReview = $this->driverReviewRepository->findWithoutFail($id);

        if (empty($driverReview)) {
            Flash::error('Driver Review not found');

            return redirect(route('driverReviews.index'));
        }

        $this->driverReviewRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.driver_review')]));

        return redirect(route('driverReviews.index'));
    }

    /**
     * Remove Media of DriverReview
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $driverReview = $this->driverReviewRepository->findWithoutFail($input['id']);
        try {
            if ($driverReview->hasMedia($input['collection'])) {
                $driverReview->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
