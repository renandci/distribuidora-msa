<?php
namespace tests\unit\Response;

use Simple\Http\Request\Adapter\AdapterInterface;
use Simple\Http\Request\Request;
use Simple\Http\Response\Response;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdapterInterface
     */
    private static $adapter;

    public function setUp()
    {
        $adapterClass = AdapterInterface::class;
        $adapter = $this->getMockBuilder($adapterClass)->getMock();
        $response = new Response(200, '{"name": "Unit"}');
        $adapter->method('getResponse')->willReturn($response);
        $adapter->method('dispatch')->willReturn(true);
        self::$adapter = $adapter;
    }

    public static function tearDownAfterClass()
    {
        self::$adapter = NULL;
    }

    public function testHttpGet()
    {
        $request = new Request(self::$adapter);
        $response = $request->get('http://google.com', ['q' => 'Test GET']);
        $this->assertEquals(200, $response->getHttpStatus());
    }

    public function testHttpPost()
    {
        $request = new Request(self::$adapter);
        $response = $request->post('http://google.com', ['q' => 'Test POST']);
        $this->assertEquals(200, $response->getHttpStatus());
    }

    public function testHttpPut()
    {
        $request = new Request(self::$adapter);
        $response = $request->put('http://google.com', ['q' => 'Test PUT']);
        $this->assertEquals(200, $response->getHttpStatus());
    }

    public function testHttpDelete()
    {
        $request = new Request(self::$adapter);
        $response = $request->delete('http://google.com', ['q' => 'Test DELETE']);
        $this->assertEquals(200, $response->getHttpStatus());
    }
}
