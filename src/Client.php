<?php
/**
 * voltdb api client, and driver wrapper
 *
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Ytake\VoltDB;

/**
 * Class Client
 * @package Ytake\VoltDB
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Client
{

    /** @var string  host */
    protected $host = "localhost"; // default

    /** @var string  api path */
    protected $path = "/api/1.0/";

    /** @var int json interface port */
    protected $apiPort = 8080; // default port

    /** @var bool ssl access */
    protected $ssl = false;

    /** @var  \stdClass  */
    private $result;

    /** @var string url */
    private $url = null;

    /** @var resource  a cURL handle on success, false on errors. */
    private $curl;

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

    /**
     * voltdb http/ json interface api access
     * wrapper, curl client
     * @param null $host
     * @param int $port
     * @param bool $ssl
     * @param null $path
     * @return $this
     */
    public function access($host = null, $port = 8080, $ssl = false, $path = null)
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

        return $this;
    }

    /**
     * GET Request
     * @param array $params
     * @return mixed|\stdClass
     */
    public function get(array $params)
    {
        $this->init();
        $merge = array_merge($this->apiParams, $params);
        $params = $this->buildQuery($merge);
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
        $this->init();
        $merge = array_merge($this->apiParams, $params);
        $params = $this->buildQuery($merge);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        return $this->exec();
    }

    /**
     * return api result
     *
     *  object(stdClass)
     *      int     status // status response
     *      array   schema  // column
     *      array   data    // return data
     * @param \stdClass $object
     * @return null|\stdClass
     */
    public function getResult(\stdClass $object)
    {
        //
        if(!$object->status) {
            return null;
        }
        $result = $object->results[0];
        // no data
        if(!count($result->data)) {
            return null;
        }
        return $result;
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
        return json_decode($result);
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
}
