<?php

namespace MahShamim\CityBank;

use Illuminate\Support\Facades\Config;

class Authenticate
{
    /**
     * @var array|mixed
     */
    public $config = [];

    /**
     * @var string
     */
    public $status = 'sandbox';

    /**
     * Authenticate constructor.
     */
    public function __construct()
    {
        $this->setConfig(Config::get('city-bank'));

        $this->setStatus(Config::get('city-bank.mode'));
    }

    /**
     * @return array|mixed
     */
    public function getConfig()
    {
        return $this->config;
    }


    /**
     * @param array|mixed $config
     */
    public function setConfig($config): void
    {
        if (is_array($config)) {
            $this->config = $config;
        } else {
            throw new \InvalidArgumentException("Invalid configuration value passed to config setter");
        }

    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        if (in_array($status, ['live', 'sandbox'])) {
            $this->status = $status;
        } else {
            throw new \InvalidArgumentException("Invalid value {$status} passed to status setter");
        }
    }

    /**
     * Do authenticate service will provide you the access token by providing following parameter value
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function doAuthenticate()
    {
        $return = 'AUTH_FAILED';
        $xml_string = <<<XML
            <auth_info xsi:type="urn:auth_info">
                <username xsi:type="xsd:string">'.$this->config[$this->status]['username'].'</username>
                <password xsi:type="xsd:string">'.$this->config[$this->status]['password'].'</password>
                <exchange_company xsi:type="xsd:string">'.$this->config[$this->status]['exchange_company'].'</exchange_company>
            </auth_info>
        XML;
        $soapMethod = 'doAuthenticate';
        $response = (new Request)->connection($xml_string, $soapMethod);
        $returnValue = json_decode($response->doAuthenticateResponse->Response, true);
        if ($returnValue['message'] == 'Successful') {
            $return = $returnValue['token'];
        }

        return $return;
    }
}
