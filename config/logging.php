<?php

use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'sms' => [
            'driver' => 'daily',
            'path' => storage_path('logs/sms/laravel.log'),
        ],

        'smsErrors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/sms/errors/laravel.log'),
        ],

        'whatsapp' => [
            'driver' => 'daily',
            'path' => storage_path('logs/whatsapp/laravel.log'),
        ],

        'whatsappErrors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/whatsapp/errors/laravel.log'),
        ],

        'canceledOrders' => [
            'driver' => 'daily',
            'path' => storage_path('logs/orders/canceled/short.log'),
        ],
        'openAndCloseRestaurant' => [
            'driver' => 'daily',
            'path' => storage_path('logs/restaurants/open_close.log'),
        ],
        'openAndCloseRestaurantErrors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/restaurants/errors/open_close.log'),
        ],

        'canceledOrdersDetails' => [
            'driver' => 'daily',
            'path' => storage_path('logs/orders/canceled/details.log'),
        ],

        'unavailableDrivers' => [
            'driver' => 'daily',
            'path' => storage_path('logs/drivers/unavailable/laravel.log'),
        ],

        'registerRestaurants' => [
            'driver' => 'daily',
            'path' => storage_path('logs/register_restaurants/laravel.log'),
        ],

        'requests' => [
            'driver' => 'daily',
            'path' => storage_path('logs/requests/laravel.log'),
        ],

    ],

];
