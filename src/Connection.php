<?php
/**
 * voltdb api client, and driver wrapper
 *
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 * @see https://voltdb.com/docs/UsingVoltDB/
 */
namespace Ytake\VoltDB;

use VoltClient;
use VoltInvocationResponse;
use Ytake\VoltDB\Exception\ResponseErrorException;
use Ytake\VoltDB\Exception\ConnectionErrorException;
use Ytake\VoltDB\Exception\MethodNotSupportedException;

/**
 * Class Connection
 * @package Ytake\LaravelVoltDB
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Connection
{

    /** @var string  localhost */
    protected $host = 'localhost';

    /** @var int  connect port */
    protected $port = 21212;

    /** @var null  */
    protected $username = null;

    /** @var null  */
    protected $password = null;

    /** @var array configure */
    protected $config;

    /** @var VoltClient */
    protected $voltdbClient;

    /** @var ParseInterface */
    protected $parse;

    /**
     * @param ParseInterface $parse
     * @param array $config
     */
    public function __construct(ParseInterface $parse, array $config = [])
    {
        $this->parse = $parse;

        $this->config = $this->setConfig();
        // database configure
        if(count($config)) {
            $this->config = array_merge($this->config, $config);
        }

        // get VoltDB Client
        $this->voltdbClient = new VoltClient();

        // connection
        $this->connect($this->config);
    }

    /**
     * return VoltClient
     * @return VoltClient
     */
    protected function client()
    {
        return new $this->voltdbClient;
    }


    /**
     * connect to voltdb
     * @param array $config
     * @throws Exception\ConnectionErrorException
     */
    protected function connect(array $config)
    {
        //
        if (!isset($config['username'])) {
            $config['username'] = null;
        }
        //
        if (!isset($config['password'])) {
            $config['password'] = null;
        }
        try{
            $connectionResult = $this->voltdbClient->connect(
                $config['host'], $config['username'], $config['password'], $config['port']
            );
        } catch(\Exception $e) {
            throw new ConnectionErrorException("voltdb connection failed", 500);
        }
        // throw exception
        if(!$connectionResult) {
            throw new ConnectionErrorException("voltdb connection refuse", 500);
        }
    }


    /**
     * Get driver name.
     * @return string
     */
    public function getDriverName()
    {
        return 'voltdb';
    }


    /**
     * get VoltDB Client
     *
     * @return VoltClient
     */
    public function getClient()
    {
        return $this->voltdbClient;
    }

    /**
     * use adhoc query
     *
     * voltdb is not support in the prepared statement(not support PDO driver)
     * @param string $query
     * @param array $bindings
     * @return array|void
     *
     * @see http://voltdb.com/docs/UsingVoltDB/sysprocadhoc.php
     */
    public function select($query, $bindings = [])
    {
        $response = $this->voltdbClient->invoke(SystemProcedure::AD_HOC, [$query]);
        return $this->getResult($response);
    }

    /**
     * use adhoc query
     *
     * voltdb is not support in the prepared statement(not support PDO driver)
     * @param string $query
     * @param array $bindings
     * @return array|void
     *
     * @see http://voltdb.com/docs/UsingVoltDB/sysprocadhoc.php
     */
    public function selectOne($query, $bindings = [])
    {
        $response = $this->voltdbClient->invoke(SystemProcedure::AD_HOC, [$query]);
        $result = $this->getResult($response);
        return (count($result)) ? $result[0] : [];
    }

    /**
     * use stored procedure
     *
     * @param $name
     * @param array $params
     * @return array
     */
    public function procedure($name, array $params = [])
    {
        $response = $this->voltdbClient->invoke($name, $params);
        return $this->getResult($response);
    }


    /**
     * @param $method
     * @param $parameters
     * @throws Exception\MethodNotSupportedException
     */
    public function __call($method, $parameters)
    {
        throw new MethodNotSupportedException("'{$method}' is not supported method", 500);
    }

    /**
     * getResult
     * @access private
     * @param VoltInvocationResponse $response
     * @return array
     * @throws Exception\ResponseErrorException
     */
    private function getResult(\VoltInvocationResponse $response = null)
    {
        $return = [];
        if ($response === null) {
            throw new ResponseErrorException("invoke had an error", 500);
        }
        $result = $this->parse->getResult($response);

        /* Iterate through all returned tables */
        while ($result->hasMoreResults()) {
            $next = $result->nextResult();
            /* Iterate through all rows in the table */
            while ($next->hasMoreRows()) {
                $return[] = $next->nextRow();
            }
        }
        return $return;
    }

    /**
     * @param null $host
     * @param null $username
     * @param null $password
     * @param null $port
     * @return mixed
     */
    public function setConfig($host = null, $username = null, $password = null, $port = null)
    {
        return $this->config = [
            'host' => (!is_null($host)) ? $host : $this->host,
            'username' => (!is_null($username)) ? $username : $this->username,
            'password' => (!is_null($password)) ? $password : $this->password,
            'port' => (!is_null($port)) ? $port : $this->port
        ];
    }
}