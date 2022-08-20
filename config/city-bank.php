<?php

return [
    'mode' => env('CITY_BANK_API_MODE', \MahShamim\CityBank\CityBank::MODE_SANDBOX), //sandbox, live
    \MahShamim\CityBank\CityBank::MODE_SANDBOX => [
        'mode' => \MahShamim\CityBank\CityBank::MODE_SANDBOX,
        'username' => env('CITY_BANK_API_USERNAME'),
        'password' => env('CITY_BANK_API_PASSWORD'),
        'company' => env('CITY_BANK_EXCHANGE_COMPANY'),
        'host' => 'nrbms.thecitybank.com',
        'base_url' => 'https://nrbms.thecitybank.com',
        'api_url' => '/nrb_api_test/dynamicApi.php?wsdl',
    ],
    \MahShamim\CityBank\CityBank::MODE_LIVE => [
        'mode' => \MahShamim\CityBank\CityBank::MODE_LIVE,
        'username' => env('CITY_BANK_API_USERNAME'),
        'password' => env('CITY_BANK_API_PASSWORD'),
        'company' => env('CITY_BANK_EXCHANGE_COMPANY'),
        'host' => 'nrbms.thecitybank.com',
        'base_url' => 'https://nrbms.thecitybank.com',
        'api_url' => '/dynamicApi.php?wsdl',
    ],
];
