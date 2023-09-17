<?php

namespace MahShamim\CityBank;

use Exception;
use InvalidArgumentException;

/**
 * Class Config
 *
 * @property string $mode current mode [sandbox, live]
 * @property string $username credentials username
 * @property string $password credentials password
 * @property string $company exchange company
 * @property string $base_url current base url
 * @property string $host current host base url
 * @property string $api_url api endpoint url starting with slash(/)
 */
class Config
{
    /**
     * Constants
     */
    const MODE_LIVE = 'live';

    const MODE_SANDBOX = 'sandbox';

    const AUTH_FAILED = 'AUTH_FAILED';

    const AUTHENTICATE = 'doAuthenticate';

    const TRANSFER = 'doTransfer';

    const TRANSFER_STATUS = 'getTnxStatus';

    const AMENDMENT_OR_CANCEL = 'doAmendmentOrCancel';

    const BALANCE = 'getBalance';

    const BKASH_CUSTOMER_VALIDATION = 'bkashCustomerValidation';

    const BKASH_TRANSFER = 'doBkashTransfer';

    const BKASH_TRANSFER_STATUS = 'getBkashTransferStatus';

    const NAGAD_CUSTOMER_VALIDATION = 'nagadCustomerValidation';

    const NAGAD_TRANSFER = 'doNagadTransfer';

    const NAGAD_TRANSFER_STATUS = 'getNagadTransferStatus';

    /**
     * Index array list of headers
     *
     * @var string[]
     */
    private $headers = ['Content-type' => 'text/xml;charset="utf-8"'];

    /**
     * Config magic variable container
     *
     * @var array
     */
    private $values = [];

    private $urls = [
        'sandbox' => [
            'base_url' => 'https://nrbms.thecitybank.com/nrb_api_test',
            'api_url' => '/dynamicApi.php?wsdl',
        ],
        'live' => [
            'base_url' => 'https://nrbms.thecitybank.com',
            'api_url' => '/dynamicApi.php?wsdl',
        ],
    ];

    /**
     * Config constructor.
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $property => $value) {
            $this->{$property} = $value;
        }

        if (! isset($this->base_url)) {
            $this->base_url = $this->urls[$this->mode]['base_url'];
        }

        if (! isset($this->api_url)) {
            $this->api_url = $this->urls[$this->mode]['api_url'];
        }
    }

    /**
     * Magic getter function for dynamic
     * value stored in values array
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->values[$key];
    }

    /**
     * Magic setter function for dynamic
     * value stored in values array
     *
     * @return void
     */
    public function __set($key, $value)
    {
        switch ($key) {
            case 'base_url':
                $this->configBaseUrl($value);
                break;

            case 'api_url':
                $this->configApiUrl($value);
                break;

            case 'mode':
                $this->configMode($value);
                break;

            default:
                $this->values[$key] = $value;
                break;
        }
    }

    private function configBaseUrl(string $url)
    {
        $metaData = parse_url($url);

        if (isset($metaData['host'])) {
            $this->setHeaders('Host', $metaData['host']);

            $this->values['host'] = $metaData['host'];

            $this->values['base_url'] = $url;
        } else {
            throw new InvalidArgumentException("Invalid value ($url) is not have host value");
        }
    }

    private function configApiUrl($url)
    {
        $this->values['api_url'] = ($this->base_url.$url);
    }

    private function configMode(string $mode)
    {
        if (in_array($mode, [self::MODE_LIVE, self::MODE_SANDBOX])) {
            $this->values['mode'] = $mode;
        } else {
            throw new InvalidArgumentException("Invalid value $mode passed to API mode setter");
        }
    }

    /**
     * Magic isset function for handle unassigned
     * value stored in values array exception
     *
     * @return bool
     *
     * @throws Exception
     */
    public function __isset($key)
    {
        if (! array_key_exists($key, $this->values)) {
            throw new Exception("Trying to access an undefined magic property $key");
        }

        return true;
    }

    /**
     * Removed magic property from value array
     */
    public function __unset($key)
    {
        if (array_key_exists($key, $this->values)) {
            unset($this->values[$key]);
        }
    }

    public function getHeaders(): array
    {
        $headers = [];

        foreach ($this->headers as $header => $value) {
            $headers[] = "{$header}: {$value}";
        }

        return $headers;
    }

    public function setHeaders($header, $value)
    {
        $this->headers[$header] = $value;
    }

    /**
     * Force overwrite  the api url
     */
    public function setApiUrl(string $api_url)
    {
        $this->api_url = $api_url;
    }
}
