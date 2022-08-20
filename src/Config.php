<?php


namespace MahShamim\CityBank;


use InvalidArgumentException;

class Config
{
    /**
     * Constants
     */
    const MODE_LIVE = 'live';

    const MODE_SANDBOX = 'sandbox';

    /**
     * @var string
     */
    private $mode = 'sandbox';

    /**
     * @var array
     */
    public $headers = [];

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var array[]
     */
    public $urls = [
        self::MODE_SANDBOX => [
            'base_url' => 'https://nrbms.thecitybank.com/nrb_api_test',
            'api_url' => '/dynamicApi.php?wsdl',
        ],
        self::MODE_LIVE => [
            'base_url' => 'https://nrbms.thecitybank.com',
            'api_url' => '/dynamicApi.php?wsdl',
        ]
    ];

    public function __construct($username, $password, $company, $mode = 'sandbox')
    {
        $this->setUsername($username);

        $this->setPassword($password);

        $this->setCompany($company);

        $this->setMode($mode);

        $this->setBaseUrl($this->urls[$this->getMode()]['base_url']);

    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        if (in_array($mode, [self::MODE_LIVE, self::MODE_SANDBOX])) {
            $this->mode = $mode;
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
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @param string $url
     */
    public function setBaseUrl($url)
    {
        $metaData = parse_url($url);

        if (isset($metaData['host'])) {
            $this->host = $metaData['host'];
            $this->baseUrl = $url;
        } else {
            throw new InvalidArgumentException("Invalid value ($url) is not have host value");
        }
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}
