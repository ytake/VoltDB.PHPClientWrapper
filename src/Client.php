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
 * Class Client
 * @package Ytake\LaravelVoltDB
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Client
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
    protected $config = [];

    /** @var VoltClient */
    protected $client;

    /** @var ParseInterface */
    protected $parse;

    /** @var resource */
    protected $resource;

    /**
     * @param VoltClient $client
     * @param ParseInterface $parse
     */
    public function __construct(VoltClient $client, ParseInterface $parse)
    {
        // result parser
        $this->parse = $parse;

        // get VoltDB Client
        $this->client = $client;
    }

    /**
     * connect to voltdb
     * @param array $config
     * @return $this
     * @throws Exception\ConnectionErrorException
     */
    public function connect(array $config = [])
    {
        $config = [
            'host' => (isset($config['host'])) ? $config['host'] : $this->host,
            'username' => (isset($config['username'])) ? $config['username'] : $this->username,
            'password' => (isset($config['password'])) ? $config['password'] : $this->password,
            'port' => (isset($config['port'])) ? $config['port'] : $this->port
        ];

        try{
            $connectionResult = $this->client->connect(
                $config['host'], $config['username'], $config['password'], $config['port']
            );
        } catch(\Exception $e) {
            throw new ConnectionErrorException("voltdb connection failed", 500);
        }
        // throw exception
        if(!$connectionResult) {
            throw new ConnectionErrorException("voltdb connection refuse", 500);
        }
        return $this;
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
        return $this->client;
    }

    /**
     * use adhoc query
     *
     * voltdb is not support in the prepared statement(not support PDO driver)
     * @param string $query
     * @return array|void
     *
     * @see http://voltdb.com/docs/UsingVoltDB/sysprocadhoc.php
     */
    public function execute($query)
    {
        $response = $this->client->invoke(SystemProcedure::AD_HOC, [$query]);
        return $this->getResult($response);
    }

    /**
     * use adhoc query
     *
     * voltdb is not support in the prepared statement(not support PDO driver)
     * @param string $query
     * @return array|void
     *
     * @see http://voltdb.com/docs/UsingVoltDB/sysprocadhoc.php
     */
    public function executeOne($query)
    {
        $response = $this->client->invoke(SystemProcedure::AD_HOC, [$query]);
        $result = $this->getResult($response);
        return (count($result)) ? $result[0] : null;
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
        $response = $this->client->invoke($name, $params);
        return $this->getResult($response);
    }

    /**
     * Asynchronous Stored Procedure Calls(resource)
     * @param $name
     * @param array $params
     * @return $this
     */
    public function asyncProcedure($name, array $params = [])
    {
        $this->resource = $this->client->invokeAsync($name, $params);
        return $this;
    }

    /**
     * A blocking call that will not return until VoltDB responds.
     * @return boolean|mixed
     */
    public function drain()
    {
        return $this->client->drain();
    }

    /**
     * @return array|null
     */
    public function asyncResult()
    {
        if(!is_null($this->resource)) {
            $response = $this->client->getResponse($this->resource);
            return $this->getResult($response);
        }
        return null;
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
     * @param VoltInvocationResponse $response
     * @return array
     * @throws Exception\ResponseErrorException
     */
    public function getResult(\VoltInvocationResponse $response = null)
    {
        $return = null;
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
     * DB connection configure
     * @param null $host
     * @param null $username
     * @param null $password
     * @param null $port
     * @return mixed
     */
    public function setConfigure($host = null, $username = null, $password = null, $port = null)
    {
        $this->host = (!is_null($host)) ? $host : $this->host;
        $this->username = (!is_null($username)) ? $username : $this->username;
        $this->password = (!is_null($password)) ? $password : $this->password;
        $this->port = (!is_null($port)) ? $port : $this->port;
        return $this;
    }

    /**
     * return configure
     * @return array
     */
    public function getConfigure()
    {
        return [
            'host' => $this->host,
            'username' => $this->username,
            'password' => $this->password,
            'port' => $this->port,
        ];
    }
}