<?php

namespace App\Http\Controllers\API;


use App\Models\Extra;
use App\Repositories\ExtraRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Food;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use App\Repositories\CustomFieldRepository;
use Flash;

/**
 * Class ExtraController
 * @package App\Http\Controllers\API
 */

class ExtraAPIController extends Controller
{
    /** @var  ExtraRepository */
    private $extraRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    public function __construct(ExtraRepository $extraRepo, CustomFieldRepository $customFieldRepo)
    {
        $this->extraRepository = $extraRepo;
        $this->customFieldRepository = $customFieldRepo;
    }

    /**
     * Display a listing of the Extra.
     * GET|HEAD /extras
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->extraRepository->pushCriteria(new RequestCriteria($request));
            $this->extraRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $extras = $this->extraRepository->all();

        /**
         * We add checked property to determind if extra linked to food or not 
         * It just for helping front-end developers to make it easy to them work on some complications in update food
         */
        if ($request->has('food_id')) {
            $food =  Food::select('id')->where('id', $request->food_id)->with('extras')->first();

            $food_extra_ids = [];
            if (isset($food->extras)) {
                $food_extra_ids =  $food->extras->pluck('id')->toArray();
            }

            foreach ($extras as $e) {
                $e->checked = in_array($e->id, $food_extra_ids);
            }
        }

        return $this->sendResponse($extras->toArray(), 'Extras retrieved successfully');
    }

    /**
     * Display the specified Extra.
     * GET|HEAD /extras/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Extra $extra */
        if (!empty($this->extraRepository)) {
            $extra = $this->extraRepository->findWithoutFail($id);
        }

        if (empty($extra)) {
            return $this->sendError('Extra not found');
        }

        return $this->sendResponse($extra->toArray(), 'Extra retrieved successfully');
    }

    /**
     * Store a newly created Extra in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->extraRepository->model());
        try {
            $extra = $this->extraRepository->create($input);
            $extra->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($extra, 'image');
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($extra->toArray(), __('lang.saved_successfully', ['operator' => __('lang.extra')]));
    }

    /**
     * Update the specified Extra in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $extra = $this->extraRepository->findWithoutFail($id);

        if (empty($extra)) {
            return $this->sendError('Extra not found');
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->extraRepository->model());
        try {
            $extra = $this->extraRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($extra, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $extra->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($extra->toArray(), __('lang.updated_successfully', ['operator' => __('lang.extra')]));
    }

    /**
     * Remove the specified Extra from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $extra = $this->extraRepository->findWithoutFail($id);

        if (empty($extra)) {
            return $this->sendError('Extra not found');
        }

        $extra = $this->extraRepository->delete($id);

        return $this->sendResponse($extra, __('lang.deleted_successfully', ['operator' => __('lang.extra')]));
    }
}
