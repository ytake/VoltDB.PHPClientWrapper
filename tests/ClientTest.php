<?php
class ClientTest extends PHPUnit_Framework_TestCase
{
    /** @var Ytake\VoltDB\Client */
    protected $client;

    public function setUp()
    {
        $this->client = new \Ytake\VoltDB\Client;
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\\VoltDB\\Client", $this->client);
    }

    /**
     * @expectedException \Ytake\VoltDB\Exception\ApiClientErrorException
     */
    public function testClientRequestGet()
    {
        // url
        $result = $this->client->access('http://localhost')->get([
                'Procedure' => 'allUser',
            ]);
        $this->assertInstanceOf("stdClass", $result);
        // url
        $result = $this->client->access('localhost')->get([
                'Procedure' => 'allUser',
            ]);
        $this->assertInstanceOf("stdClass", $result);
        // ssl failed
        $result = $this->client->access('https://localhost')->get([
                'Procedure' => 'allUser',
            ]);
        $this->assertInstanceOf("stdClass", $result);
        // ssl failed
        $client = $this->client->access('localhost', 8080, true);
        $this->assertSame("https://localhost:8080/api/1.0/", $client->getUrl());

         $client->get([
                'Procedure' => 'allUser',
            ]);
        $this->assertInstanceOf("stdClass", $result);
    }

    /**
     * @expectedException \Ytake\VoltDB\Exception\ApiClientErrorException
     */
    public function testClientRequestPost()
    {
        // url
        $result = $this->client->access('http://localhost')->post([
                'Procedure' => 'allUser',
            ]);
        $this->assertInstanceOf("stdClass", $result);
        // url
        $result = $this->client->access('localhost')->post([
                'Procedure' => 'allUser',
            ]);
        $this->assertInstanceOf("stdClass", $result);
        // ssl failed
        $result = $this->client->access('https://localhost')->post([
                'Procedure' => 'allUser',
            ]);
        $this->assertInstanceOf("stdClass", $result);
        // ssl failed
        $client = $this->client->access('localhost', 8080, true);
        $this->assertSame("https://localhost:8080/api/1.0/", $client->getUrl());

        $client->post([
                'Procedure' => 'allUser',
            ]);
        var_dump([]);
        $this->assertInstanceOf("stdClass", $result);
    }
} 