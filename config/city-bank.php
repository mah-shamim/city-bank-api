<?php
/**
 * Created by PhpStorm.
 * User: MD ARIFUL HAQUE
 * Date: 7/10/2020
 * Time: 1:00 AM
 */


return [
    'mode'  => env('CITY_BANK_API_MODE','sandbox'), //sandbox, live
    'sandbox' => [
        'username'              => env('CITY_BANK_API_USERNAME'),
        'password'              => env('CITY_BANK_API_PASSWORD'),
        'exchange_company'      => env('CITY_BANK_EXCHANGE_COMPANY'),
        'app_host'              => env('CITY_BANK_API_HOST'),
    ],
    'live' => [
        'username'              => env('CITY_BANK_API_USERNAME'),
        'password'              => env('CITY_BANK_API_PASSWORD'),
        'exchange_company'      => env('CITY_BANK_EXCHANGE_COMPANY'),
        'app_host'              => env('CITY_BANK_API_HOST'),
    ],
];
