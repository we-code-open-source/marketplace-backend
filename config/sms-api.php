<?php

return [

    'country_code' => '218', //Country code to be added
    'default' => env('SMS_API_DEFAULT_GATEWAY', 'easysms'), //Choose default gateway

    //    Basic Gateway Sample Configuration
    'easysms' => [
        'method' => 'GET', //Choose Request Method (GET/POST) Default:GET
        'url' =>  env('SMS_API_BASE_URL', ''), //Base URL
        'params' => [
            'send_to_param_name' => 'to', //Send to Parameter Name
            'msg_param_name' => 'sms', //Message Parameter Name
            'others' => [
                "action" => 'send-sms',
                "api_key" => env('SMS_API_KEY', ''),
                "unicode" => true,
            ],
        ],
        'headers' => [
            'Accept' => 'application/json',
        ],
        'add_code' => false, //Include Country Code (true/false)
    ],

];
