<?php
namespace tests\unit\Response;

use Simple\Http\Response\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Response
     */
    private static $response;

    public static function setUpBeforeClass()
    {
        self::$response = new Response(201, 'TestUnit', [20 => 'Error Fake']);
    }

    public static function tearDownAfterClass()
    {
        self::$response = NULL;
    }

    public function testHttpStatus()
    {
        $this->assertEquals(201, self::$response->getHttpStatus());
    }

    public function testRawBody()
    {
        $this->assertEquals('TestUnit', self::$response->getRawBody());
    }

    public function testErrorCount()
    {
        $this->assertCount(1, self::$response->getErrors());
    }
}
