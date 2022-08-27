<?php

namespace MahShamim\CityBank;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use MahShamim\CityBank\Commands\InstallCommand;

class CityBankServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/city-bank.php' => config_path('city-bank.php'),
        ], 'city-bank-config');


        if ($this->app->runningInConsole()) {
            $this->commands(InstallCommand::class);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/city-bank.php',
            'city-bank'
        );

        $this->app->singleton('city-bank', function ($app) {

            $mode = Config::get('city-bank.mode');

            $config = Config::get("city-bank.{$mode}");

            return new CityBank($config);
        });

        $this->app->alias('city-bank', CityBank::class);
    }
}
