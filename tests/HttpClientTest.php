<?php

/**
 * Class HttpClientTest
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class HttpClientTest extends PHPUnit_Framework_TestCase
{
    /** @var Ytake\VoltDB\HttpClient */
    protected $client;

    public function setUp()
    {
        parent::setUp();
        $this->client = new \Ytake\VoltDB\HttpClient(new \Ytake\VoltDB\Parse);
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\\VoltDB\\HttpClient", $this->client);
    }

    public function testInsert()
    {
        $result = $this->client->request('http://localhost')->get([
                'Procedure' => 'addUser',
                'Parameters' => [rand(), 'test']
            ])->getResult();
        $this->assertInstanceOf('stdClass', $result);
    }

    /**
     * @expectedException \Ytake\VoltDB\Exception\StatusErrorException
     */
    public function testOverlapInsert()
    {
        $this->client->request('http://localhost')->get([
                'Procedure' => 'addUser',
                'Parameters' => [1, 'test']
            ]);
        $this->client->request('http://localhost')->get([
                'Procedure' => 'addUser',
                'Parameters' => [1, 'test']
            ]);
    }

    /**
     * @expectedException \Ytake\VoltDB\Exception\ApiClientErrorException
     */
    public function testClientRequestGet()
    {
        // url
        $result = $this->client->request('http://localhost')->get([
                'Procedure' => 'allUser',
            ])->getResult();
        $this->assertInstanceOf("stdClass", $result);
        // url
        $result = $this->client->request('localhost')->get([
                'Procedure' => 'allUser',
            ])->getResult();
        $this->assertInstanceOf("stdClass", $result);
        // ssl failed
        $result = $this->client->request('https://localhost')->get([
                'Procedure' => 'allUser',
            ])->getResult();
        $this->assertInstanceOf("stdClass", $result);
        // ssl failed
        $client = $this->client->request('localhost', 8080, true);
        $this->assertSame("https://localhost:8080/api/1.0/", $client->getUrl());

        $client->get([
                'Procedure' => 'allUser',
            ])->getResult();
        $this->assertInstanceOf("stdClass", $result);
    }

    /**
     * @expectedException \Ytake\VoltDB\Exception\ApiClientErrorException
     */
    public function testClientRequestPost()
    {
        // url
        $result = $this->client->request('http://localhost')->post([
                'Procedure' => 'allUser',
            ])->getResult();
        $this->assertInstanceOf("stdClass", $result);
        // url
        $result = $this->client->request('localhost')->post([
                'Procedure' => 'allUser',
            ])->getResult();
        $this->assertInstanceOf("stdClass", $result);
        // ssl failed
        $result = $this->client->request('https://localhost')->post([
                'Procedure' => 'allUser',
            ])->getResult();
        $this->assertInstanceOf("stdClass", $result);
        // ssl failed
        $client = $this->client->request('localhost', 8080, true);
        $this->assertSame("https://localhost:8080/api/1.0/", $client->getUrl());

        $client->post([
                'Procedure' => 'allUser',
            ])->getResult();
        $this->assertInstanceOf("stdClass", $result);
    }

    /**
     * get SystemInformation
     */
    public function testSystemInfo()
    {
        //
        $result = $this->client->request('http://localhost')->info()->getResult();
        $this->assertInstanceOf("stdClass", $result);
        $result = $this->client->request('http://localhost')->info("DEPLOYMENT")->getResult();
        $this->assertInstanceOf("stdClass", $result);
    }

    public function testUrl()
    {
        $this->assertNull($this->client->getUrl());
    }
} 