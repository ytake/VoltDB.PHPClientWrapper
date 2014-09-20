<?php
/**
 * voltdb api client, and driver wrapper
 *
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ytake\VoltDB;

/**
 * Class HttpClient
 * @package Ytake\VoltDB
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class HttpClient
{

    /** @var string  host */
    protected $host = "localhost"; // default

    /** @var string  api path */
    protected $path = "/api/1.0/";

    /** @var int json interface port */
    protected $apiPort = 8080; // default port

    /** @var bool ssl access */
    protected $ssl = false;

    /** @var string url */
    private $url = null;

    /** @var resource  a cURL handle on success, false on errors. */
    private $curl;

    /** @var \stdClass  result */
    private $result;

    /** @var string cipher */
    private $cipher = null;

    /**
     * string url arguments
     */
    /** @var array  */
    private $apiParams = [
        // procedure-name
        'Procedure' => null,
        // procedure-parameters
        'Parameters' => null,
        // username for authentication
        'User' => null,
        // password for authentication
        'Password' => null,
        // Hashed password for authentication
        'Hashedpassword' => null,
        // true|false
        'admin' => false,
        // function-name
        'jsonp' => null
    ];

    /** @var ParseInterface */
    protected $parse;

    /**
     * @param ParseInterface $parse
     */
    public function __construct(ParseInterface $parse)
    {
        $this->parse = $parse;
    }


    /**
     * voltdb http/ json interface api access
     * wrapper, curl client
     * @param null $host
     * @param int $port
     * @param bool $ssl
     * @param null $path
     * @return $this
     */
    public function request($host = null, $port = 8080, $ssl = false, $path = null)
    {
        $this->host = (!is_null($host)) ? $host : $this->host;
        $this->apiPort = (!is_null($port)) ? $port : $this->apiPort;
        $this->path = (!is_null($path)) ? $path : $this->path;
        $parsed = parse_url($this->host);

        $this->ssl = ($ssl) ? $ssl : $this->ssl;
        $protocol = (!$this->ssl) ? "http" : "https";

        if(isset($parsed['scheme'])) {
            $protocol = $parsed['scheme'];
            $this->host = $parsed['host'];
            $this->ssl = ($parsed['scheme'] === 'http') ? false : true;
        }
        $this->url = "{$protocol}://{$this->host}:{$this->apiPort}{$this->path}";
        return $this;
    }

    /**
     * initialize curl client
     * @return $this
     */
    protected function init()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        if($this->ssl) {
            curl_setopt($this->curl, CURLOPT_SSLVERSION, 3);
        }
        if($this->cipher) {
            curl_setopt($this->curl, CURLOPT_SSL_CIPHER_LIST, $this->cipher);
        }
        return $this;
    }

    /**
     * GET Request
     * @param array $params
     * @return mixed|\stdClass
     */
    public function get(array $params)
    {
        $params = $this->preparedRequest($params);
        curl_setopt($this->curl, CURLOPT_URL, "{$this->url}?{$params}");
        return $this->exec();
    }

    /**
     * POST Request
     * @param array $params
     * @return mixed|\stdClass
     */
    public function post(array $params)
    {
        $params = $this->preparedRequest($params);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        return $this->exec();
    }

    /**
     * prepared Request(curl)
     * @access private
     * @param array $params
     * @return string
     */
    private function preparedRequest(array $params)
    {
        $merge = array_merge($this->apiParams, $params);
        return $this->init()->buildQuery($merge);
    }

    /**
     * get SystemInformation / default OVERVIEW
     * @param string $component
     * @return mixed|\stdClass
     */
    public function info($component = "OVERVIEW")
    {
        $this->init();
        $this->apiParams['Procedure'] = SystemProcedure::SYSTEM_INFO;
        $this->apiParams['Parameters'] = [$component];
        $params = $this->buildQuery($this->apiParams);
        curl_setopt($this->curl, CURLOPT_URL, "{$this->url}?{$params}");
        return $this->exec();
    }

    /**
     * buildQuery
     *
     * json_encode "Parameters"
     * @param array $array
     * @return string
     */
    protected function buildQuery(array $array)
    {
        $result = [];
        array_walk($array, function($value, $key) use (&$result) {
                if("Parameters" === $key) {
                    if(!is_null($value)) {
                        $value = json_encode($value);
                    }
                }
                $result[$key] = $value;
            });
        unset($array);
        return http_build_query($result);
    }

    /**
     * curl_exec
     * @return \stdClass|mixed
     * @throws Exception\ApiClientErrorException
     */
    private function exec()
    {
        $result = curl_exec($this->curl);
        // curl error
        if(!$result) {
            throw new Exception\ApiClientErrorException(curl_error($this->curl), curl_errno($this->curl));
        }
        curl_close($this->curl);
        $this->result = $this->parse->getResult(json_decode($result));
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getParam()
    {
        return $this->apiParams;
    }

    /**
     * @return \stdClass
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @todo
     * @param string $cipher
     * @return void
     */
    public function setCipher($cipher = null)
    {
        if(!is_null($cipher)) {
            $this->cipher = $cipher;
        }
    }

    /**
     * @todo
     * @return string
     */
    public function getCipher()
    {
        return $this->cipher;
    }
}