<?php

namespace MahShamim\CityBank;

use Exception;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;

class CityBank
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
     * @var string
     */
    public $apiUrl = '';

    /**
     * @var array
     */
    public $headers = [];

    /**
     * CityBank constructor.
     * @param array $config
     * @param string $status
     */
    public function __construct($config = [], $status = 'sandbox')
    {
        $this->setConfig($config);
        $this->setStatus($status);
        $this->setApiUrl();
        $this->setHeaders('Content-type: text/xml;charset="utf-8"');
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
    public function setConfig($config)
    {
        if (is_array($config)) {
            $this->config = $config;
        } else {
            throw new InvalidArgumentException('Invalid configuration value passed to config setter');
        }
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        if (in_array($status, ['live', 'sandbox'])) {
            $this->status = $status;
        } else {
            throw new InvalidArgumentException("Invalid value $status passed to status setter");
        }
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param string|null $apiUrl send full url default config
     */
    public function setApiUrl($apiUrl = null)
    {
        $this->apiUrl = (is_null($apiUrl))
            ? ($this->config['base_url'] . $this->config['api_url'])
            : $apiUrl;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string|array $headers
     */
    public function setHeaders(...$headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Do authenticate service will provide you the access token by providing following parameter value
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function authenticate()
    {
        $return = 'AUTH_FAILED';

        $authPayload = trim('
            <auth_info xsi:type="urn:auth_info">
                <username xsi:type="xsd:string">' . (isset($this->config['username']) ? $this->config['username'] : '') . '</username>
                <password xsi:type="xsd:string">' . (isset($this->config['password']) ? $this->config['password'] : '') . '</password>
                <exchange_company xsi:type="xsd:string">' . (isset($this->config['company']) ? $this->config['company'] : '') . '</exchange_company>
            </auth_info>
            ');

        $response = $this->connect($authPayload, 'doAuthenticate');

        $returnValue = json_decode($response->doAuthenticateResponse->Response, true);

        if ($returnValue['message'] == 'Successful') {
            $return = $returnValue['token'];
        }

        return $return;
    }

    /**
     * @param $xml_post_string
     * @param $method
     * @return \SimpleXMLElement
     *
     * @throws Exception
     */
    protected function connect($xml_post_string, $method)
    {
        $payload = $this->wrapper($xml_post_string, $method);

        $contentLength = strlen($payload);

        $this->setHeaders("Content-length: {$contentLength}",
            "SOAPAction: {$method}",
            "Host: {$this->config['host']}"
        );

        $formattedResponse = "";

        if (!function_exists('curl_version')) {
            throw new Exception("Curl extension is not enabled.", 500);
        }

        $request = curl_init();

        try {
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($request, CURLOPT_TIMEOUT, 0);
            curl_setopt($request, CURLOPT_POST, true);
            curl_setopt($request, CURLOPT_POSTFIELDS, $payload); // the SOAP request
            curl_setopt($request, CURLOPT_HTTPHEADER, $this->getHeaders());
            curl_setopt($request, CURLOPT_URL, $this->getApiUrl());
            $response = curl_exec($request);
            if ($response === false) {
                $this->handleException($request, $method);
            }

            $formattedResponse = str_replace(['<SOAP-ENV:Body>', '</SOAP-ENV:Body>', 'xmlns:ns1="urn:dynamicapi"', 'ns1:'], '', $response);

            logger("{$method} <br/> {$formattedResponse}");

        } catch (Exception $exception) {
            $this->handleException($request, $method);
        } finally {
            curl_close($request);
        }

        return simplexml_load_string($formattedResponse);

    }

    /**
     * Wrapping the request object as proper xml object stream
     *
     * @param $content
     * @param $method
     * @return string
     */
    private function wrapper($content, $method)
    {
        return trim('
        <?xml version="1.0" encoding="utf-8"?>
            <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:dynamicapi">
                <soapenv:Header/>
                <soapenv:Body>
                    <urn:' . $method . ' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                        ' . $content . '
                    </urn:' . $method . '>
                </soapenv:Body>
            </soapenv:Envelope>
        ');
    }

    /**
     * @param $request
     * @param $method
     * @return void
     * @throws Exception
     */
    private function handleException($request, $method)
    {
        if (App::environment('production') == true) {
            logger("{$method} CURL Request Error : " . curl_error($request));
        } else {
            throw new Exception("{$method} CURL Request Error : " . curl_error($request), curl_errno($request));
        }
    }
}
