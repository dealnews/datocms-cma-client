<?php

namespace DealNews\DatoCMS\CMA\Tests\Exception;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Exception\Unknown;

/**
 * Tests for the Unknown exception class
 */
class UnknownTest extends TestCase {

    #[Group('unit')]
    public function testConstructorSetsMessage() {
        $exception = new Unknown('Unexpected error occurred');

        $this->assertEquals('Unexpected error occurred', $exception->getMessage());
    }

    #[Group('unit')]
    public function testConstructorSetsCode() {
        $exception = new Unknown('Unexpected error', 1000);

        $this->assertEquals(1000, $exception->getCode());
    }

    #[Group('unit')]
    public function testConstructorSetsPreviousException() {
        $previous = new \Exception('Original error');
        $exception = new Unknown('Wrapped error', 1000, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Group('unit')]
    public function testExceptionIsInstanceOfRuntimeException() {
        $exception = new Unknown('Unexpected error');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    #[Group('unit')]
    public function testCanWrapAnyThrowable() {
        $error = new \TypeError('Type mismatch');
        $exception = new Unknown('Wrapped type error', 1000, $error);

        $this->assertSame($error, $exception->getPrevious());
        $this->assertEquals('Type mismatch', $exception->getPrevious()->getMessage());
    }
}
