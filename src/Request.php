<?php


namespace MahShamim\CityBank;


use Exception;
use SimpleXMLElement;

class Request
{
    /**
     * @var Config
     */
    private $config;

    private $method;

    private $payload;

    /**
     * Request constructor.
     * @param Config $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param $method
     * @return $this
     */
    public function method($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param $data
     * @return Request
     * @throws Exception
     */
    public function payload($data)
    {
        $this->payload = $this->preparePayload($data);

        return $this;
    }

    /**
     * @param $data
     * @return string
     * @throws Exception
     */
    public function preparePayload($data)
    {
        if (empty($this->method)) {
            throw new Exception("Payload Generate Exception , Request method is set.");
        }

        $content = '';

        foreach ($data as $wrapper => $fields) {
            $content .= '<' . $wrapper . ' xsi:type="urn:' . $wrapper . '">' . PHP_EOL;
            foreach ($fields as $title => $field) {
                $content .= ('<' . $title . ' xsi:type="xsd:' . gettype($field) . '">' . $field . '</' . $title . '>' . PHP_EOL);
            }
            $content .= '</' . $wrapper . '>' . PHP_EOL;
        }

        return $this->wrapper($content);
    }

    /**
     * Wrapping the request object as proper xml object stream
     *
     * @param $content
     * @return string
     */
    private function wrapper($content)
    {
        return trim('
        <?xml version="1.0" encoding="utf-8"?>
            <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:dynamicapi">
                <soapenv:Header/>
                <soapenv:Body>
                    <urn:' . $this->method . ' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                        ' . $content . '
                    </urn:' . $this->method . '>
                </soapenv:Body>
            </soapenv:Envelope>
        ');
    }

    /**
     * @return false|SimpleXMLElement|string|null
     * @throws Exception
     */
    public function connect()
    {
        $formattedResponse = '';

        if (!function_exists('curl_version')) {
            throw new Exception('Curl extension is not enabled.', 500);
        }

        $client = curl_init();

        try {
            $curlOptions = $this->curlOptions();

            curl_setopt_array($client, $curlOptions);

            $response = curl_exec($client);

            if ($response === false) {
                $this->handleException($client);
            }

            $formattedResponse = str_replace([
                '<SOAP-ENV:Body>',
                '</SOAP-ENV:Body>',
                'xmlns:ns1="urn:dynamicapi"',
                'ns1:'],
                '',
                $response
            );

            logger("{$this->method} <br/> {$formattedResponse}");

        } catch (Exception $exception) {
            $this->handleException($client);
        } finally {
            curl_close($client);
            $this->cleanup();
        }

        return simplexml_load_string($formattedResponse);
    }

    /**
     * @return array
     */
    private function curlOptions()
    {
        $contentLength = strlen($this->payload);

        $this->config->setHeaders(
            "Content-length: {$contentLength}",
            "SOAPAction: {$this->method}"
        );

        return [
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_ANY,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $this->payload,
            CURLOPT_HTTPHEADER => $this->config->getHeaders(),
            CURLOPT_URL => $this->config->api_url,
        ];
    }

    /**
     * @param $request
     * @return void
     *
     * @throws Exception
     */
    private function handleException($request)
    {
        if ($this->config->mode == Config::MODE_LIVE) {
            logger("{$this->method} Request Error : " . curl_error($request));
        } else {
            throw new Exception("{$this->method} Request Error : " . curl_error($request), curl_errno($request));
        }
    }

    private function cleanup()
    {
        $this->payload = '';
        $this->method = '';
    }
}
