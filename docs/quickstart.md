# Quick Start

This package provides an easy interface and support for integrating the city bank NRBMS web service API. This
implementation is written for use on a PHP programming language-based application with the additional composer required.
If you don't have support for composer package manager please clone the GitHub repository and initial package using your
main class following configuration manuals.

## Installation

To get start using this package follow these instructions. You can install the package via composer:

```bash
composer require mah-shamim/city-bank-api
```

## Configuration

This package support all the major frameworks and tested on both
[Laravel](https://larvel.com) & [CodeIgniter](https://codeigniter.com) framework. It is developed to support plain php
project with composer autoload enabled. You need to publish the config file with:

### Laravel

For laravel application package is compact with config publish and installation command.

- **Installation command**:
  This artisan command follow through publishing configuration and appending the required environment variables to
  application.
    ```bash
    php artisan city-bank:install
    ```
- **Config publish command**:
  This is default laravel configuration file publish command. you have to copy the environment variables from
  configuration file.
    ```bash
    php artisan vendor:publish --tag="city-bank-config"
    ```

### Non-Laravel

As non-laravel application has a different way of storing configuration and autoload class. please this instruction to
configure basic settings.

- Package main class constructor expect an array of configuration values you can create separate
  file to store configuration and pass as a first argument of class constructor.
  
    ```php
    $config = [
        'mode' => 'sandbox', # sandbox, live
        'username' => 'CITY_BANK_API_USERNAME',
        'password' => 'CITY_BANK_API_PASSWORD',
        'company' => 'CITY_BANK_EXCHANGE_COMPANY',
        'base_url' => 'https://nrbms.thecitybank.com/nrb_api_test',
        'api_url' => '/dynamicApi.php?wsdl',
    ];
  
  $cityBank = new \MahShamim\CityBank\CityBank($config);
    ```
  
> **Note**: *base_url* for is the entrypoint on API and vary for testing and production environment
  
Done. Now you can fully utilize every form elements from these package

# Environments

This package has basic form element style that is supported by bootstrap. Some basic form styles are given below:

1. **Sandbox/UAT environment**
    - Please provide your environment IP address that needed to be whitelisted in our system.
    - Once your IP is whitelisted you will receive an email with the access credential for test environment.
    - **Endpoint**: http://nrbms.thecitybank.com/nrb_api_test/dynamicApi.php?wsdl
2. **Production environment**
    - The process will remain same to get the production web service access.
    - **Endpoint**: http://nrbms.thecitybank.com/dynamicApi.php?wsdl
