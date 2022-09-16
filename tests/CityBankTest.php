<?php

namespace MahShamim\CityBank\Tests;

use MahShamim\CityBank\CityBank;
use PHPUnit\Framework\TestCase;

class CityBankTest extends TestCase
{

    /**
     * @return CityBank
     * @throws \Exception
     */
    private function createInstance()
    {
        $config = [
            'mode' => $_ENV['mode'] ?? "sandbox",
            'username' => $_ENV['username'],
            'password' => $_ENV['password'],
            'company' => $_ENV['company'],
            'base_url' => $_ENV['base_url'] ?? "https://nrbms.thecitybank.com",
            'api_url' => $_ENV['api_url'] ?? "/nrb_api_test/dynamicApi.php?wsdl",
        ];

        $cityBank = new CityBank($config);

        return $cityBank->init();
    }

    /**
     * Test to check if API response a valid auth token
     *
     * @return void
     * @throws \Exception
     */
    public function test_auth_token()
    {
        $instance = $this->createInstance();

        $result = $instance->token();;

        $this->assertRegExp('/[a-zA-Z0-9]{33}/', $result, "Invalid Authentication Token : {$result}");
    }



}
