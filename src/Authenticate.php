<?php

namespace MahShamim\CityBank;

class Authenticate
{
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
        $xml_string = '
            <auth_info xsi:type="urn:auth_info">
                <username xsi:type="xsd:string">'.$this->config[$this->status]['username'].'</username>
                <password xsi:type="xsd:string">'.$this->config[$this->status]['password'].'</password>
                <exchange_company xsi:type="xsd:string">'.$this->config[$this->status]['exchange_company'].'</exchange_company>
            </auth_info>
        ';
        $soapMethod = 'doAuthenticate';
        $response = (new Request)->connection($xml_string, $soapMethod);
        $returnValue = json_decode($response->doAuthenticateResponse->Response, true);
        if ($returnValue['message'] == 'Successful') {
            $return = $returnValue['token'];
        }

        return $return;
    }
}
