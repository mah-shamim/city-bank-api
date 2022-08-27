<?php

namespace MahShamim\CityBank;

use Exception;
use JsonException;
use SimpleXMLElement;

class Request
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $methodWrapper;

    /**
     * @var array
     */
    private $payload = [];

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $responseWrapper;

    /**
     * @var string
     */
    private $wrapper;

    /**
     * Request constructor.
     * @param Config $config
     * @throws Exception
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function method($method)
    {
        $this->methodWrapper = $method;

        $this->responseWrapper = "{$method}Response";

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     * @throws Exception
     */
    public function payload($wrapper, $data = [])
    {
        $this->payload = $data;

        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getXml()
    {
        return $this->preparePayload();
    }

    /**
     * @return mixed|string
     * @throws Exception
     */
    public function connect()
    {
        $response = '';

        if (!function_exists('curl_version')) {
            throw new Exception('Curl extension is not enabled.', 500);
        }

        $client = curl_init();

        try {
            $curlOptions = $this->curlOptions();

            curl_setopt_array($client, $curlOptions);

            $response = curl_exec($client);

            if (strlen($response) == 0 && $response == false) {
                throw new Exception("Curl response is empty");
            }
        } catch (Exception $exception) {
            $this->handleException($client, $exception);
        }

        logger("API Response" . $response);

        curl_close($client);
        return $this->formatResponse($response);
    }

    /**
     * @return array
     * @throws Exception
     */
    private function curlOptions()
    {
        $xmlString = $this->preparePayload();

        $this->config->setHeaders("Content-length", strlen($xmlString));
        $this->config->setHeaders("SOAPAction", $this->methodWrapper);

        return [
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_ANY,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $xmlString,
            CURLOPT_HTTPHEADER => $this->config->getHeaders(),
            CURLOPT_URL => $this->config->api_url,
        ];
    }

    /**
     * @param string $response
     * @throws Exception
     */
    private function formatResponse($response = '')
    {
        \Log::info("$this->methodWrapper \n " . $response);

        $response = trim(str_replace([
            '<SOAP-ENV:Body>',
            '</SOAP-ENV:Body>',
            'xmlns:ns1="urn:dynamicapi"',
            'ns1:'], '', $response));

        try {
            $response = new SimpleXMLElement($response);

            $response = ($response instanceof SimpleXMLElement)
                ? json_decode(json_encode($response), true)
                : '';

            if (isset($response[$this->responseWrapper]['Response'])) {
                $response = json_decode($response[$this->responseWrapper]['Response'], true);

                if (json_last_error() != JSON_ERROR_NONE) {
                    throw new JsonException(json_last_error_msg(), json_last_error());
                }
            }

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        } finally {
            $this->cleanup();
            return $response;
        }
    }

    /**
     * Render the payload array to a xml response string
     *
     * @return string
     * @throws Exception
     */
    private function preparePayload()
    {
        $content = '';

        if ($this->methodWrapper != Config::AUTHENTICATE) {
            if (is_null($this->token)) {
                throw new Exception("Authenticate Token is missing");
            }

            $this->payload['token'] = $this->token;
        }

        foreach ($this->payload as $title => $field) {
            $type = strtolower(gettype($field));
            if ($type == 'null') {
                $type = "string";
            }

            $content .= ("                            <$title xsi:type=\"xsd:$type\">$field</$title>\n");
        }

        return $this->wrapper(trim($content));
    }

    /**
     * Wrapping the request object as proper xml object stream
     *
     * @param $content
     * @return string
     * @throws Exception
     */
    private function wrapper($content)
    {
        if (empty($this->methodWrapper)) {
            throw new Exception("Payload method is missing.");
        }

        return ('<?xml version="1.0" encoding="utf-8"?>
            <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:dynamicapi">
                <soapenv:Header/>
                <soapenv:Body>
                    <urn:' . $this->methodWrapper . ' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                        <' . $this->wrapper . ' xsi:type="urn:' . $this->wrapper . '">
                            ' . $content . '
                       </' . $this->wrapper . '>
                    </urn:' . $this->methodWrapper . '>
                </soapenv:Body>
            </soapenv:Envelope>');
    }

    /**
     * @param $request
     * @param null $exception
     * @return void
     *
     * @throws Exception
     */
    private function handleException($request, $exception)
    {
        if ($this->config->mode == Config::MODE_LIVE) {
            logger("$this->methodWrapper Request Error : " . curl_error($request));
        } else {
            throw new Exception("$this->methodWrapper Exception : {$exception->getMessage()},  Curl Error: " . curl_error($request), curl_errno($request));
        }
    }

    private function cleanup()
    {
        $this->payload = [];
        $this->methodWrapper = null;
        $this->responseWrapper = null;
        $this->wrapper = null;
    }

}
