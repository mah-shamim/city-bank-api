# City Bank Remittance API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mah-shamim/city-bank-api.svg?style=flat-square)](https://packagist.org/packages/mah-shamim/city-bank-api)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/mah-shamim/city-bank-api/run-tests?label=tests)](https://github.com/mah-shamim/city-bank-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/mah-shamim/city-bank-api/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/mah-shamim/city-bank-api/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mah-shamim/city-bank-api.svg?style=flat-square)](https://packagist.org/packages/mah-shamim/city-bank-api)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require mah-shamim/city-bank-api
```

## Usage

###For Non-Laravel Projects
```php
$config = [
        'mode' => 'sandbox',
        'username' => 'CITY_BANK_API_USERNAME',
        'password' => 'CITY_BANK_API_PASSWORD',
        'company' => 'CITY_BANK_EXCHANGE_COMPANY',
        'base_url' => 'https://nrbms.thecitybank.com',
        'api_url' => '/nrb_api_test/dynamicApi.php?wsdl',
    ];
    

$cityBank = new \MahShamim\CityBank($config);
```

###For Laravel & Lumen

You can set up whole configuration using this command
```bash
php artisan city-bank:install
```

OR

You can publish the config file with(Laravel & Lumen):
```bash
php artisan vendor:publish --tag="city-bank-config"
```

This is the contents of the published config file:

```php
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
```
## Changelog

Please see [CHANGELOG](docs/CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/mah-shamim/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [MD ARIFUL HAQUE](https://github.com/mah-shamim)
- [Mohammad Hafijul Islam](https://github.com/hafijul233)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support us

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).
