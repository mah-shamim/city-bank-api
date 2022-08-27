<?php

namespace MahShamim\CityBank\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class CityBank
 *
 * @method static \MahShamim\CityBank\CityBank transfer(array $transferData = [])
 * @method static \MahShamim\CityBank\CityBank getTnxStatus(string $reference)
 * @method static \MahShamim\CityBank\CityBank doAmendmentOrCancel(string $reference, string $details = '?')
 * @method static \MahShamim\CityBank\CityBank getBalance()
 * @method static \MahShamim\CityBank\CityBank bkashCustomerValidation(string $mobileNumber, string $fullName = '?')
 * @method static \MahShamim\CityBank\CityBank doBkashTransfer(array $data = [])
 * @method static \MahShamim\CityBank\CityBank bkashTnxStatus()
 * @method static string|null token()
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
