<?php

/**
 * Class ConnectionTest
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class ConnectionTest extends PHPUnit_Framework_TestCase
{
    /** @var Ytake\VoltDB\Connection */
    protected $connection;

    public function setUp()
    {
        $this->connection = new \Ytake\VoltDB\Connection(new \Ytake\VoltDB\Parse);
    }

    /**
     *  @expectedException \Ytake\VoltDB\Exception\ConnectionErrorException
     */
    public function testConnection()
    {
        $client = $this->connection->getClient();
        $this->assertInstanceOf('VoltClient', $client);

        $config = $this->connection->setConfig('localhostt');
        // throw Exception, failed host
        new \Ytake\VoltDB\Connection(new \Ytake\VoltDB\Parse, $config);
    }

    public function testAdHocQuery()
    {
        $this->assertInternalType('array', $this->connection->select("SELECT * FROM users"));
    }

    /**
     *  @expectedException \Ytake\VoltDB\Exception\StatusErrorException
     */
    public function testProcedure()
    {
        $this->assertInternalType('array', $this->connection->procedure("allUser"));
        // throw Exception, allUser not Parameters
        $this->assertInternalType('array', $this->connection->procedure("allUser", ['a']));
    }
} 