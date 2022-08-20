<?php
/**
 * Created by PhpStorm.
 * User: MD ARIFUL HAQUE
 * Date: 7/10/2020
 * Time: 1:00 AM
 */

return [
    'mode' => env('CITY_BANK_API_MODE', 'sandbox'), //sandbox, live
    'sandbox' => [
        'mode' => 'sandbox',
        'username' => env('CITY_BANK_API_USERNAME'),
        'password' => env('CITY_BANK_API_PASSWORD'),
        'company' => env('CITY_BANK_EXCHANGE_COMPANY'),
        'host' => env('CITY_BANK_API_SANDBOX_HOST'),
        'url' => '/nrb_api_test/dynamicApi.php?wsdl',
        'secure' => true, //[true => https , false => http ]
    ],
    'live' => [
        'mode' => 'live',
        'username' => env('CITY_BANK_API_USERNAME'),
        'password' => env('CITY_BANK_API_PASSWORD'),
        'company' => env('CITY_BANK_EXCHANGE_COMPANY'),
        'host' => env('CITY_BANK_API_LIVE_HOST'),
        'url' => '/dynamicApi.php?wsdl',
        'secure' => true, //[true => https , false => http ]
    ],
];
