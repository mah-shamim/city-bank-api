<?php


namespace MahShamim\CityBank;


use InvalidArgumentException;

/**
 * Class Config
 * @property string $mode current mode [sandbox, live]
 * @property string $username credentials username
 * @property string $password credentials password
 * @property string $company exchange company
 * @property string $base_url current base url
 * @property string $host current host base url
 * @property string $api_url api endpoint url starting with slash(/)
 *
 * @package MahShamim\CityBank
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
    protected $values = [];

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
     * Magic getter function for dynamic value stored in values array
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->values[$key];
    }

    /**
     * Magic setter function for dynamic value stored in values array
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
     * @param string $url
     */
    public function configBaseUrl($url)
    {
        $metaData = parse_url($url);

        if (isset($metaData['host'])) {
            $this->values['host'] = $metaData['host'];
            $this->values['base_url'] = $url;
        } else {
            throw new InvalidArgumentException("Invalid value ($url) is not have host value");
        }
    }
}
