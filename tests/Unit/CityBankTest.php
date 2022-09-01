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
     */
    public function testCityBankExecuteAuthenticate()
    {
        $cityBank = new CityBank();

        $result = $cityBank->doAuthenticate();

        $this->assertIsArray($result, 'Successful');
    }
}
