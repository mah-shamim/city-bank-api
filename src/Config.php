<?php


namespace MahShamim\CityBank;


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
 *
 * @package MahShamim\CityBank\Config
 */
class Config
{
    /**
     * Constants
     */
    const MODE_LIVE = 'live';

    const MODE_SANDBOX = 'sandbox';

    /**
     * Index array list of headers
     *
     * @var string[]
     */
    private $headers = [
        'Content-type: text/xml;charset="utf-8"'
    ];

    /**
     * Config magic variable container
     *
     * @var array
     */
    private $values = [];

    /**
     * Magic getter function for dynamic
     * value stored in values array
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->values[$key];
    }

    /**
     * Magic isset function for handle unassigned
     * value stored in values array exception
     * @param $key
     * @return void
     * @throws \Exception
     */
    public function __isset($key)
    {
        if (!array_key_exists($key, $this->values)) {
            throw  new \Exception("Trying to access an undefined magic property $key");
        }
    }

    /**
     * Removed magic property from value array
     *
     * @param $key
     */
    public function __unset($key)
    {
        if (array_key_exists($key, $this->values)) {
            unset($this->values[$key]);
        }

    }

    /**
     * Config constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        foreach ($options as $property => $value) {
            $this->{$property} = $value;
        }
    }

    /**
     * Magic setter function for dynamic
     * value stored in values array
     *
     * @param $key
     * @param $value
     * @return void
     */
    public function __set($key, $value)
    {
        switch ($key) {

            case 'base_url' :
                $this->configBaseUrl($value);
                break;

            case 'api_url' :
                $this->configApiUrl($value);
                break;

            case 'mode' :
                $this->configMode($value);
                break;

            default :
                $this->values[$key] = $value;
                break;
        }
    }

    /**
     * @param string $mode
     */
    public function configMode($mode)
    {
        if (in_array($mode, [self::MODE_LIVE, self::MODE_SANDBOX])) {
            $this->values['mode'] = $mode;
        } else {
            throw new InvalidArgumentException("Invalid value $mode passed to API mode setter");
        }
    }

    /**
     * @param string $url
     */
    public function configBaseUrl($url)
    {
        $metaData = parse_url($url);

        if (isset($metaData['host'])) {

            $this->setHeaders("Host: {$metaData['host']}");

            $this->values['host'] = $metaData['host'];

            $this->values['base_url'] = $url;
        } else {
            throw new InvalidArgumentException("Invalid value ($url) is not have host value");
        }
    }

    /**
     * @param $url
     */
    public function configApiUrl($url)
    {
        $this->values['api_url'] = ($this->base_url . $url);
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
     * Force overwrite  the api url
     *
     * @param string $api_url
     */
    public function setApiUrl($api_url)
    {
        $this->api_url = $api_url;
    }
}
