<?php


namespace MahShamim\CityBank;


use SimpleXMLElement;

class Request
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Request constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param $payload
     * @param $method
     * @return false|SimpleXMLElement|string|null
     */
    public function connect($payload, $method)
    {
        $xmlString = $this->preparePayload($payload, $method);

        $contentLength = strlen($payload);

        $this->config->setHeaders(
            "Content-length: {$contentLength}",
            "SOAPAction: {$method}"
        );

        $formattedResponse = '';

        if (!function_exists('curl_version')) {
            throw new Exception('Curl extension is not enabled.', 500);
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

    public function preparePayload($data, $method)
    {
        $content = '';

        foreach ($data as $wrapper => $fields) {
            $content .= '<' . $wrapper . ' xsi:type="urn:' . $wrapper . '">' . PHP_EOL;
            foreach ($fields as $title => $field) {
                $content .= ('<' . $title . ' xsi:type="xsd:' . gettype($field) . '">' . $field . '</' . $title . '>' . PHP_EOL);
            }
            $content .= '</' . $wrapper . '>' . PHP_EOL;
        }

        return $this->wrapper($content, $data);
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
}
