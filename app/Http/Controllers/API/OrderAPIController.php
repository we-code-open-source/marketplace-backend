<?php

/**
 * File name: OrderAPIController.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Http\Controllers\API;


use App\Criteria\Orders\OrdersOfStatusesCriteria;
use App\Criteria\Orders\OrdersOfUserCriteria;
use App\Criteria\Users\AdminsCriteria;
use App\Events\OrderChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\AssignedOrder;
use App\Notifications\NewOrder;
use App\Notifications\StatusChangedOrder;
use App\Repositories\CartRepository;
use App\Repositories\FoodOrderRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Stripe\Token;
use App\Events\CreatedOrderEvent;
use App\Notifications\OrderNeedsToAccept;
use App\Services\AddOrderToFirebaseService;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderController
 * @package App\Http\Controllers\API
 */
class OrderAPIController extends Controller
{
    /** @var  OrderRepository */
    private $orderRepository;
    /** @var  FoodOrderRepository */
    private $foodOrderRepository;
    /** @var  CartRepository */
    private $cartRepository;
    /** @var  UserRepository */
    private $userRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;
    /** @var  NotificationRepository */
    private $notificationRepository;

    /**
     * OrderAPIController constructor.
     * @param OrderRepository $orderRepo
     * @param FoodOrderRepository $foodOrderRepository
     * @param CartRepository $cartRepo
     * @param PaymentRepository $paymentRepo
     * @param NotificationRepository $notificationRepo
     * @param UserRepository $userRepository
     */
    public function __construct(OrderRepository $orderRepo, FoodOrderRepository $foodOrderRepository, CartRepository $cartRepo, PaymentRepository $paymentRepo, NotificationRepository $notificationRepo, UserRepository $userRepository)
    {
        $this->orderRepository = $orderRepo;
        $this->foodOrderRepository = $foodOrderRepository;
        $this->cartRepository = $cartRepo;
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepo;
        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Display a listing of the Order.
     * GET|HEAD /orders
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->orderRepository->pushCriteria(new RequestCriteria($request));
            $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfStatusesCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $orders = $this->orderRepository->all();

        return $this->sendResponse($orders->toArray(), 'Orders retrieved successfully');
    }

    /**
     * Display the specified Order.
     * GET|HEAD /orders/{id}
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var Order $order */
        if (!empty($this->orderRepository)) {
            try {
                $this->orderRepository->pushCriteria(new RequestCriteria($request));
                $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $order = $this->orderRepository->findWithoutFail($id);
        }

        if (empty($order)) {
            return $this->sendError('Order not found');
        }

        return $this->sendResponse($order->toArray(), 'Order retrieved successfully');
    }


    /**
     * Display a listing of the open Order.
     * GET|HEAD /orders
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function open(Request $request)
    {
        try {
            $request->merge(['statuses' => [20, 30, 40, 50, 60, 70]]);
            $this->orderRepository->pushCriteria(new RequestCriteria($request));
            $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfStatusesCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $orders = $this->orderRepository->all();
        return $this->sendResponse($orders->toArray(), 'Orders retrieved successfully');
    }


    /**
     * Store a newly created Order in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $payment = $request->only('payment');
        if (isset($payment['payment']) && $payment['payment']['method']) {
            if ($payment['payment']['method'] == "Credit Card (Stripe Gateway)") {
                return $this->stripPayment($request);
            } else {
                return $this->cashPayment($request);
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    private function stripPayment(Request $request)
    {
        $input = $request->all();
        $amount = 0;
        try {
            $user = $this->userRepository->findWithoutFail($input['user_id']);
            if (empty($user)) {
                return $this->sendError('User not found');
            }
            $stripeToken = Token::create(array(
                "card" => array(
                    "number" => $input['stripe_number'],
                    "exp_month" => $input['stripe_exp_month'],
                    "exp_year" => $input['stripe_exp_year'],
                    "cvc" => $input['stripe_cvc'],
                    "name" => $user->name,
                )
            ));
            if ($stripeToken->created > 0) {
                if (empty($input['delivery_address_id'])) {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'hint')
                    );
                } else {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint')
                    );
                }
                foreach ($input['foods'] as $foodOrder) {
                    $foodOrder['order_id'] = $order->id;
                    $amount += $foodOrder['price'] * $foodOrder['quantity'];
                    $this->foodOrderRepository->create($foodOrder);
                }
                $amount += $order->delivery_fee;
                $amountWithTax = $amount + ($amount * $order->tax / 100);
                $charge = $user->charge((int)($amountWithTax * 100), ['source' => $stripeToken]);
                $payment = $this->paymentRepository->create([
                    "user_id" => $input['user_id'],
                    "description" => trans("lang.payment_order_done"),
                    "price" => $amountWithTax,
                    "status" => $charge->status, // $charge->status
                    "method" => $input['payment']['method'],
                ]);
                $this->orderRepository->update(['payment_id' => $payment->id], $order->id);

                $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);

                Notification::send($order->foodOrders[0]->food->restaurant->users, new NewOrder($order));
                $order->payment_id = $payment->id;
                event(new CreatedOrderEvent($order));
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    private function cashPayment(Request $request)
    {
        $input = $request->all();
        $amount = 0;
        try {
            DB::beginTransaction();
            $order = $this->orderRepository->create($request->only('user_id', 'order_status_id', 'tax', 'delivery_coupon_value', 'delivery_coupon_id', 'restaurant_coupon_value', 'restaurant_coupon_id', 'delivery_address_id', 'delivery_fee', 'restaurant_delivery_fee', 'hint'));
            foreach ($input['foods'] as $foodOrder) {
                $foodOrder['order_id'] = $order->id;
                $amount += $foodOrder['price'] * $foodOrder['quantity'];
                $this->foodOrderRepository->create($foodOrder);
            }
            $amount += $order->delivery_fee;
            $amountWithTaxAndCoupon = $amount + ($amount * $order->tax / 100) - $order->delivery_coupon_value - $order->restaurant_coupon_value;
            $payment = $this->paymentRepository->create([
                "user_id" => $input['user_id'] ?? null,
                "description" => trans("lang.payment_order_waiting"),
                "price" => $amountWithTaxAndCoupon,
                "status" => 'Waiting for Client',
                "method" => $input['payment']['method'],
            ]);

            $restaurant_id = $order->foodOrders->first()->food->restaurant_id;
            /* if ($request->phone) {
                $unregistered_customer = $order->unregisteredCustomer()->create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'restaurant_id' => $restaurant_id,
                ]);
                $order->setRelation('unregisteredCustomer', $unregistered_customer);
            } */
            if ($request->has('unregistered_customer')) {
                $unregistered_customer = $order->unregisteredCustomer()->create(array_merge(
                    $request->unregistered_customer,
                    [
                        'restaurant_id' => $restaurant_id,
                    ]
                ));
                $order->setRelation('unregisteredCustomer', $unregistered_customer);

                if ($request->has('delivery_address')) {
                    $delivery_address = $unregistered_customer->address()->create($request->delivery_address);
                    $order->setRelation('deliveryAddress', $delivery_address);
                }
            }

            if ($order->user_id) {
                $order->order_status_id =  20; // 20 : waiting_for_restaurant
            } else {
                $order->order_status_id =  30; // 30 : accepted_from_restaurant
            }
            $order->restaurant_id =  $restaurant_id;
            $order->unregistered_customer_id =  $unregistered_customer->id ?? null;
            $order->delivery_address_id =  $delivery_address->id ?? $request->get('delivery_address_id');
            $order->payment_id = $payment->id;
            $order->save();

            // if order dose not have user_id , that means order for unregistered_customer from restaurant
            // so user_id of cart is restaurant (manager users) id 
            // so we can clear cart by manager id (auth user = manager) 
            $this->cartRepository->deleteWhere(['user_id' => $order->user_id ?? auth()->user()->id]);


            // start load users who will receive notification
            $this->userRepository->pushCriteria(new AdminsCriteria());
            $this->userRepository->scopeQuery(function ($q) {
                return $q->where('active', true)
                    ->with('deviceTokens')
                    ->select('id', 'name');
            });
            $users_for_notifications = $this->userRepository->all();

            if ($order->user_id) {
                $users_for_notifications->merge($order->foodOrders[0]->food->restaurant->getUsersWhoEnabledNotifications());
            }
            Notification::send($users_for_notifications, new NewOrder($order));

            // start update number of use for coupons
            if ($order->delivery_coupon_id) {
                $order->deliveryCoupon->count_used += 1;
                $order->deliveryCoupon->save();
            }
            if ($order->restaurant_coupon_id) {
                $order->restaurantCoupon->count_used += 1;
                $order->restaurantCoupon->save();
            }
            // end update number of use for coupons

            DB::commit();
        } catch (ValidatorException $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }

    /**
     * Update the specified Order in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $oldOrder = $this->orderRepository->findWithoutFail($id);
        if (empty($oldOrder)) {
            return $this->sendError('Order not found');
        }
        $oldStatus = $oldOrder->payment->status;
        $input = $request->all();

        try {
            $order = $this->orderRepository->update($input, $id);
            if (isset($input['order_status_id']) && $input['order_status_id'] == 80 /* delivered */ && !empty($order)) {
                $this->paymentRepository->update(['status' => 'Paid'], $order['payment_id']);
            }
            event(new OrderChangedEvent($oldStatus, $order));

            if (setting('enable_notifications', false)) {
                if ($order->user && isset($input['order_status_id']) && $input['order_status_id'] != $oldOrder->order_status_id) {
                    Notification::send([$order->user], new StatusChangedOrder($order));
                }

                if (isset($input['driver_id']) && ($input['driver_id'] != $oldOrder['driver_id'])) {
                    $driver = $this->userRepository->findWithoutFail($input['driver_id']);
                    if (!empty($driver)) {
                        Notification::send([$driver], new AssignedOrder($order));
                    }
                }
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }



    /**
     * Select order to delivery by current driver (auth user).
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delivery($id, Request $request)
    {
        $order = Order::where('order_status_id', 10)->findOrFail($id);  // 10 : waiting_for_drivers

        /* if ($order->user_id) {
            $order->order_status_id = 20; // 20 : waiting_for_restaurant
            if (setting('send_sms_notifications_for_restaurants', false) || setting('send_whatsapp_notifications_for_restaurants', false)) {
                Notification::send($order->restaurant->getUsersWhoEnabledNotifications(), new OrderNeedsToAccept($order));
            }
        } else {
            $order->order_status_id = 30; // 30 : accepted_from_restaurant
        } */

        $order->order_status_id = 40; // 40 : driver_assigned
        $order->driver_id = auth()->user()->id;
        $order->save();

        //app('firebase.firestore')->getFirestore()->collection('orders')->document($order->id)->delete();

        return $this->sendResponse([], __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }

    /**
     * Cancel driver order by current driver (auth user).
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel($id, Request $request)
    {
        // 20 : waiting_for_restaurant
        // 40 : driver_assigned
        $order = Order::where('driver_id', auth()->user()->id)->whereIn('order_status_id', [20, 40])->findOrFail($id);

        /* $order->foodOrders()->firstOrFail() // validate restauran dose not have private drivers
            ->food()->firstOrFail()
            ->restaurant()->select('id')->where('private_drivers', false)->firstOrFail(); */

        $order->order_status_id = 130; // 130 : canceled_from_driver 
        $order->save();

        $lifetime = (int)setting('order_expiration_time_before_accept_for_drivers');
        /**
         * I subtract 15 seconds from time becuase it will not make sense to add order to firestore
         * and waitting for drivers and restaurants to accept order in 15 seconds only , it is not enough time for doing this  
         */
        $lifetime -= 15;

        if ($order->created_at > now()->addSeconds($lifetime * -1)) {
            new AddOrderToFirebaseService($order);
        }

        return $this->sendResponse([], __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }
}
