<?php

return [
    'mode' => env('CITY_BANK_API_MODE', \MahShamim\CityBank\Config::MODE_SANDBOX), //sandbox, live
    'sandbox' => [
        'mode' => \MahShamim\CityBank\Config::MODE_SANDBOX,
        'username' => env('CITY_BANK_API_USERNAME'),
        'password' => env('CITY_BANK_API_PASSWORD'),
        'company' => env('CITY_BANK_EXCHANGE_COMPANY'),
        'base_url' => 'https://nrbms.thecitybank.com/nrb_api_test',
        'api_url' => '/dynamicApi.php?wsdl',
    ],
    'live' => [
        'mode' => \MahShamim\CityBank\Config::MODE_LIVE,
        'username' => env('CITY_BANK_API_USERNAME'),
        'password' => env('CITY_BANK_API_PASSWORD'),
        'company' => env('CITY_BANK_EXCHANGE_COMPANY'),
        'base_url' => 'https://nrbms.thecitybank.com',
        'api_url' => '/dynamicApi.php?wsdl',
    ],
];
