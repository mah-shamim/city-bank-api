<?php

namespace MahShamim\CityBank;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use MahShamim\CityBank\Commands\CityBankCommand;

class CityBankServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('city-bank-api')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_city-bank-api_table')
            ->hasCommand(CityBankCommand::class);
    }
}
