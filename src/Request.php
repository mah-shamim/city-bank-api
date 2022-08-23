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

    /**
     * @var string
     */
    private $method = '';

    /**
     * @var array
     */
    private $payload = [];

    /**
     * @var string
     */
    private $token = '';

    /**
     * Request constructor.
     * @param Config $config
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
        $this->method = $method;

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     * @throws Exception
     */
    public function payload(array $data)
    {
        if (is_array($data)) {
            $this->payload = $data;
        }

        return $this;
    }

    /**
     * Authenticate service will provide you the access token by providing following parameter value.
     *
     * @return self
     * @throws Exception
     */
    public function token()
    {
        $payload = [
            'auth_info' => [
                'username' => $this->config->username,
                'password' => $this->config->password,
                'exchange_company' => $this->config->company,
            ]
        ];

        $response = $this
            ->method(Config::METHOD_AUTHENTICATE)
            ->payload($payload)
            ->connect();

        if ($response instanceof \SimpleXMLElement) {
            $jsonResponse = json_decode($response->doAuthenticateResponse->Response, true);

            if ($jsonResponse['token'] != Config::AUTH_FAILED) {
                $this->token = $jsonResponse['token'];
            }
        }

        return $this;
    }

    /**
     * Render the payload array to a xml response string
     *
     * @return string
     * @throws Exception
     */
    private function preparePayload()
    {
        if (empty($this->method)) {
            throw new Exception("Payload method is missing.");
        }

        dd($this->method);

        if ($this->method != Config::METHOD_AUTHENTICATE && empty($this->token)) {
            throw new Exception("Payload token is missing.");
        }

        $content = "";

        foreach ($this->payload as $wrapper => $fields) {

            $content .= "\t\t\t\t<{$wrapper} xsi:type=\"urn:{$wrapper}\">\n";

            if ($this->method != Config::METHOD_AUTHENTICATE) {
                $fields['token'] = $this->token;
            }

            foreach ($fields as $title => $field) {

                $type = gettype($field);

                if ($type == 'NULL') {
                    $type = "string";
                }

                $content .= ("\t\t\t\t\t<{$title} xsi:type=\"xsd:{$type}\">{$field}</{$title}>\n");
            }

            $content .= "\t\t\t\t</{$wrapper}>";
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

            dump($response);

            /*if ($response === false) {
                $this->handleException($client);
            }*/

            $formattedResponse = str_replace([
                '<SOAP-ENV:Body>',
                '</SOAP-ENV:Body>',
                'xmlns:ns1="urn:dynamicapi"',
                'ns1:'], '', $response);

        } catch (Exception $exception) {
            $this->handleException($client, $exception);
        } finally {
            curl_close($client);
            $this->cleanup();
        }

        return simplexml_load_string($formattedResponse);
    }

    /**
     * @return array
     * @throws Exception
     */
    private function curlOptions()
    {
        $xmlString = $this->preparePayload();

        $contentLength = strlen($xmlString);

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
            CURLOPT_POSTFIELDS => $xmlString,
            CURLOPT_HTTPHEADER => $this->config->getHeaders(),
            CURLOPT_URL => $this->config->api_url,
        ];
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
            logger("{$this->method} Request Error : " . curl_error($request));
        } else {
            throw new Exception("{$this->method} Exception : {$exception->getMessage()},  Curl Error: " . curl_error($request), curl_errno($request));
        }
    }

    private function cleanup()
    {
        $this->payload = '';
        $this->method = '';
    }
}
