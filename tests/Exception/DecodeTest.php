<?php

namespace DealNews\DatoCMS\CMA\Tests\Exception;

use DealNews\DatoCMS\CMA\Exception\Decode;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Decode exception class
 */
class DecodeTest extends TestCase {

    #[Group('unit')]
    public function testConstructorSetsMessage() {
        $exception = new Decode('Failed to decode JSON');

        $this->assertEquals('Failed to decode JSON', $exception->getMessage());
    }

    #[Group('unit')]
    public function testConstructorSetsCode() {
        $exception = new Decode('Failed to decode JSON', 1001);

        $this->assertEquals(1001, $exception->getCode());
    }

    #[Group('unit')]
    public function testConstructorSetsPreviousException() {
        $previous  = new \JsonException('Syntax error');
        $exception = new Decode('Failed to decode JSON', 1001, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Group('unit')]
    public function testConstructorSetsRawJson() {
        $raw_json  = '{invalid json}';
        $exception = new Decode('Failed to decode JSON', 1001, null, $raw_json);

        $this->assertEquals($raw_json, $exception->getRawJson());
    }

    #[Group('unit')]
    public function testGetRawJsonReturnsNullWhenNotSet() {
        $exception = new Decode('Failed to decode JSON');

        $this->assertNull($exception->getRawJson());
    }

    #[Group('unit')]
    public function testExceptionIsInstanceOfRuntimeException() {
        $exception = new Decode('Failed to decode JSON');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    #[Group('unit')]
    public function testFullConstructorWithAllParameters() {
        $previous = new \JsonException('Syntax error');
        $raw_json = '{"incomplete":';

        $exception = new Decode(
            'JSON decode failed',
            1001,
            $previous,
            $raw_json
        );

        $this->assertEquals('JSON decode failed', $exception->getMessage());
        $this->assertEquals(1001, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
        $this->assertEquals($raw_json, $exception->getRawJson());
    }

    #[Group('unit')]
    public function testRawJsonCanBeEmptyString() {
        $exception = new Decode('Empty response', 1001, null, '');

        $this->assertEquals('', $exception->getRawJson());
    }
}
