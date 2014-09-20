<?php

/**
 * Class ClientTest
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    /** @var Ytake\VoltDB\Client */
    protected $connection;

    public function setUp()
    {
        $this->connection = new \Ytake\VoltDB\Client(new \VoltClient, new \Ytake\VoltDB\Parse);
    }

    /**
     * @expectedException \Ytake\VoltDB\Exception\ConnectionErrorException
     */
    public function testConnection()
    {
        $testHost = 'localhostt';
        $client = $this->connection->getClient();
        $this->assertInstanceOf('VoltClient', $client);
        // instance
        $this->assertInstanceOf('Ytake\VoltDB\Client', $this->connection->connect());

        /** connection Error throw */
        $this->connection->setConfigure($testHost);
        $this->connection->connect();

        $configure = $this->connection->getConfigure();
        $this->assertSame($configure['host'], $testHost);
    }

    /**
     * @expectedException \Ytake\VoltDB\Exception\StatusErrorException
     */
    public function testProcedure()
    {
        $this->connection->connect()->procedure("addUser", [time(), 'test']);
        $procedure = $this->connection->connect()->procedure("allUser");
        $this->assertInternalType('array', $procedure);
        // throw Exception, allUser not Parameters
        $this->assertInternalType('array', $this->connection->connect()->procedure("allUser", ['a']));
    }

    public function testAdHocQuery()
    {
        $this->connection->connect()->execute("SELECT * FROM users");
        $this->assertInternalType('array', $this->connection->connect()->execute("SELECT * FROM users"));
    }

    /**
     *
     */
    public function testAsync()
    {
        $async = $this->connection->connect()->asyncProcedure("allUser");
        // blocking and get result
        $result = $async->drain()->asyncResult();
        $this->assertInternalType('array', $result);
    }
} 