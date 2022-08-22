<?php

namespace MahShamim\CityBank\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class CityBank
 *
 * @method static string authenticate()
 *
 * @see \MahShamim\CityBank\CityBank
 */
class CityBank extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'city-bank';
    }
}
