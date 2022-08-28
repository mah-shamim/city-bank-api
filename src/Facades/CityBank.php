<?php

namespace MahShamim\CityBank\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class CityBank
 *
 * @method static \MahShamim\CityBank\CityBank doAuthenticate(string $username = null, string $password = null, string $company = null)
 * @method static \MahShamim\CityBank\CityBank doTransfer(string $reference, array $data = [])
 * @method static \MahShamim\CityBank\CityBank getTnxStatus(string $reference)
 * @method static \MahShamim\CityBank\CityBank doAmendmentOrCancel(string $reference, string $details = '?')
 * @method static \MahShamim\CityBank\CityBank getBalance()
 * @method static \MahShamim\CityBank\CityBank doBkashCustomerValidation(string $mobileNumber, string $fullName = '?')
 * @method static \MahShamim\CityBank\CityBank doBkashTransfer(string $reference, array $data = [])
 * @method static \MahShamim\CityBank\CityBank getBkashTnxStatus(string $reference)
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
