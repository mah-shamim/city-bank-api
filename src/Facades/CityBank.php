<?php

namespace MahShamim\CityBank\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class CityBank
 *
 * @method static string transfer(array $transferData = [])
 * @method static string authenticate()
 * @method static string transactionStatus(array $inputs_data = [])
 * @method static string cancel(array $transferData = [])
 * @method static string balance()
 * @method static string bkashCustomerValidation()
 * @method static string bkashValidation()
 * @method static string bkashTransfer()
 * @method static string bkashTnxStatus()
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
