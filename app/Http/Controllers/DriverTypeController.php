<?php

namespace App\Http\Controllers;

use App\DataTables\DriverTypeDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateDriverTypeRequest;
use App\Http\Requests\UpdateDriverTypeRequest;
use App\Repositories\DriverTypeRepository;
use App\Repositories\CustomFieldRepository;

use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class DriverTypeController extends Controller
{
    /** @var  DriverTypeRepository */
    private $driverTypeRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;



    public function __construct(DriverTypeRepository $driverTypeRepo, CustomFieldRepository $customFieldRepo)
    {
        parent::__construct();
        $this->driverTypeRepository = $driverTypeRepo;
        $this->customFieldRepository = $customFieldRepo;
    }

    /**
     * Display a listing of the DriverType.
     *
     * @param DriverTypeDataTable $driverTypeDataTable
     * @return Response
     */
    public function index(DriverTypeDataTable $driverTypeDataTable)
    {
        return $driverTypeDataTable->render('driver_types.index');
    }

    /**
     * Show the form for creating a new DriverType.
     *
     * @return Response
     */
    public function create()
    {
        $hasCustomField = in_array($this->driverTypeRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->driverTypeRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('driver_types.create')->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created DriverType in storage.
     *
     * @param CreateDriverTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateDriverTypeRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->driverTypeRepository->model());
        try {
            $driverType = $this->driverTypeRepository->create($input);
            $driverType->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.driver_type')]));

        return redirect(route('driverTypes.index'));
    }

    /**
     * Display the specified DriverType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $driverType = $this->driverTypeRepository->findWithoutFail($id);

        if (empty($driverType)) {
            Flash::error('Driver Type not found');

            return redirect(route('driverTypes.index'));
        }

        return view('driver_types.show')->with('driverType', $driverType);
    }

    /**
     * Show the form for editing the specified DriverType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $driverType = $this->driverTypeRepository->findWithoutFail($id);

        if (empty($driverType)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.driver_type')]));

            return redirect(route('driverTypes.index'));
        }
        $customFieldsValues = $driverType->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->driverTypeRepository->model());
        $hasCustomField = in_array($this->driverTypeRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('driver_types.edit')->with('driverType', $driverType)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified DriverType in storage.
     *
     * @param  int              $id
     * @param UpdateDriverTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDriverTypeRequest $request)
    {
        $driverType = $this->driverTypeRepository->findWithoutFail($id);

        if (empty($driverType)) {
            Flash::error('Driver Type not found');
            return redirect(route('driverTypes.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->driverTypeRepository->model());
        try {
            $driverType = $this->driverTypeRepository->update($input, $id);
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $driverType->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.driver_type')]));

        return redirect(route('driverTypes.index'));
    }

    /**
     * Remove the specified DriverType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $driverType = $this->driverTypeRepository->findWithoutFail($id);

        if (empty($driverType)) {
            Flash::error('Driver Type not found');

            return redirect(route('driverTypes.index'));
        }

        $this->driverTypeRepository->delete($id);
        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.driver_type')]));
        return redirect(route('driverTypes.index'));
    }

    /**
     * Remove Media of DriverType
     * @param Request $request
     */
    /* public function removeMedia(Request $request)
    {
        $input = $request->all();
        $driverType = $this->driverTypeRepository->findWithoutFail($input['id']);
        try {
            if ($driverType->hasMedia($input['collection'])) {
                $driverType->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    } */
}
