<?php

namespace MahShamim\CityBank\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class CityBank
 *
 * @method static \MahShamim\CityBank\CityBank transfer(array $transferData = [])
 * @method static \MahShamim\CityBank\CityBank transactionStatus(array $inputs_data = [])
 * @method static \MahShamim\CityBank\CityBank cancel(array $transferData = [])
 * @method static \MahShamim\CityBank\CityBank balance()
 * @method static \MahShamim\CityBank\CityBank bkashValidation()
 * @method static \MahShamim\CityBank\CityBank bkashTransfer()
 * @method static \MahShamim\CityBank\CityBank bkashTnxStatus()
 * @method static string|null token()
 * @method static string|null toXML()
 * @method static mixed get()
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
