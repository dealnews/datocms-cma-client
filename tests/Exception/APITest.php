<?php

namespace DealNews\DatoCMS\CMA\Tests\Exception;

use DealNews\DatoCMS\CMA\Exception\API;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API exception class
 */
class APITest extends TestCase {

    #[Group('unit')]
    public function testConstructorSetsMessage() {
        $exception = new API('Test message');

        $this->assertEquals('Test message', $exception->getMessage());
    }

    #[Group('unit')]
    public function testConstructorSetsCode() {
        $exception = new API('Test message', 404);

        $this->assertEquals(404, $exception->getCode());
    }

    #[Group('unit')]
    public function testConstructorSetsPreviousException() {
        $previous  = new \RuntimeException('Previous error');
        $exception = new API('Test message', 500, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Group('unit')]
    public function testConstructorSetsResponseBody() {
        $response_body = '{"errors": [{"message": "Not found"}]}';
        $exception     = new API('Test message', 404, null, $response_body);

        $this->assertEquals($response_body, $exception->getResponseBody());
    }

    #[Group('unit')]
    public function testGetResponseBodyReturnsNullWhenNotSet() {
        $exception = new API('Test message');

        $this->assertNull($exception->getResponseBody());
    }

    #[Group('unit')]
    public function testExceptionIsInstanceOfRuntimeException() {
        $exception = new API('Test message');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    #[Group('unit')]
    public function testFullConstructorWithAllParameters() {
        $previous      = new \RuntimeException('Previous error');
        $response_body = '{"errors": [{"message": "Server error"}]}';

        $exception = new API(
            'API Error',
            500,
            $previous,
            $response_body
        );

        $this->assertEquals('API Error', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
        $this->assertEquals($response_body, $exception->getResponseBody());
    }
}
