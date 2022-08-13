<?php

namespace MahShamim\CityBank\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use MahShamim\CityBank\CityBankServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MahShamim\\CityBank\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            CityBankServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_city-bank-api_table.php.stub';
        $migration->up();
        */
    }
}
