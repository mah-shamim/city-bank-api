<?php

namespace MahShamim\CityBank\Tests\Unit;

use MahShamim\CityBank\CityBank;
use PHPUnit\Framework\TestCase;

class CityBankTest extends TestCase
{
    /**
     * Test can check authenticate return array
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testCityBankExecuteAuthenticate()
    {
        $config = [
            'mode' => 'sandbox',
            'username' => 'cbl_mycash_online',
            'password' => 'Myash0nlin3',
            'company' => 'MyCash Online',
            'base_url' => 'https://nrbms.thecitybank.com',
            'api_url' => '/nrb_api_test/dynamicApi.php?wsdl',
        ];

        $cityBank = new CityBank($config);

        $result = $cityBank->init()->doAuthenticate();

        $this->assertIsArray($result, 'Successful');
    }
}
