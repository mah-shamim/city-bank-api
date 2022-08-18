<?php


namespace MahShamim\CityBank;


use Illuminate\Support\Facades\Log;

class Request
{
    public function __construct(){
        $this->config = config('city-bank');
        if ($this->config['mode'] === 'sandbox') {
            $this->apiUrl = 'https://'.$this->config[$this->status]['app_host'].'/nrb_api_test/dynamicApi.php?wsdl';
            $this->status = 'sandbox';

        } else {
            $this->apiUrl = 'https://'.$this->config[$this->status]['app_host'].'/dynamicApi.php?wsdl';
            $this->status = 'live';
        }
    }

    /**
     * @param $xml_post_string
     * @param $method
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function connection($xml_post_string, $method)
    {
        $xml_string = $this->xmlGenerate($xml_post_string, $method);
        Log::info($method.'<br>'.$xml_string);
        $headers = [
            "Host: " . $this->config[$this->status]['app_host'],
            "Content-type: text/xml;charset=\"utf-8\"",
            "Content-length: " . strlen($xml_string),
            "SOAPAction: " .$method,
        ];

        // PHP cURL  for connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // execution
        $response = curl_exec($ch);
        Log::error($method.' CURL reported error: ');
        if ($response === false) {
            throw new \Exception(curl_error($ch), curl_errno($ch));
        }
        curl_close($ch);
        $response1 = str_replace("<SOAP-ENV:Body>", "", $response);
        $response2 = str_replace("</SOAP-ENV:Body>", "", $response1);
        $response = str_replace('xmlns:ns1="urn:dynamicapi"', '', $response2);
        $response = str_replace('ns1:', '', $response);//dd($response);
        Log::info($method.'<br>'.$response);
        return simplexml_load_string($response);
    }

    /**
     * @param $string
     * @param $method
     * @return string
     */
    public function xmlGenerate($string, $method)
    {
        $xml_string = '<?xml version="1.0" encoding="utf-8"?>
            <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:dynamicapi">
                <soapenv:Header/>
                <soapenv:Body>
                    <urn:'.$method.' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                        '.$string.'
                    </urn:'.$method.'>
                </soapenv:Body>
            </soapenv:Envelope>
        ';
        return $xml_string;
    }
}
