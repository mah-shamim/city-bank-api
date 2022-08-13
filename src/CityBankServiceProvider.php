<?php

namespace MahShamim\CityBank;

use MahShamim\CityBank\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CityBankServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('city-bank')
            ->hasConfigFile()
            ->hasCommand(InstallCommand::class);
    }
}
