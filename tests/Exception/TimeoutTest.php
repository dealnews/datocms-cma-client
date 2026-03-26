<?php

namespace DealNews\DatoCMS\CMA\Tests\Exception;

use DealNews\DatoCMS\CMA\Exception\Timeout;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Timeout exception class
 */
class TimeoutTest extends TestCase {

    #[Group('unit')]
    public function testConstructorSetsJobId() {
        $exception = new Timeout(
            'Test timeout',
            0,
            'job-123',
            15.5
        );

        $this->assertEquals('job-123', $exception->getJobId());
    }

    #[Group('unit')]
    public function testConstructorSetsElapsedTime() {
        $exception = new Timeout(
            'Test timeout',
            0,
            'job-123',
            15.5
        );

        $this->assertEquals(15.5, $exception->getElapsedTime());
    }

    #[Group('unit')]
    public function testConstructorSetsMessage() {
        $exception = new Timeout(
            'Job polling timeout after 30s',
            0,
            'job-123',
            30.0
        );

        $this->assertEquals(
            'Job polling timeout after 30s',
            $exception->getMessage()
        );
    }

    #[Group('unit')]
    public function testConstructorSetsCode() {
        $exception = new Timeout(
            'Test timeout',
            42,
            'job-123',
            15.5
        );

        $this->assertEquals(42, $exception->getCode());
    }

    #[Group('unit')]
    public function testConstructorWithPreviousException() {
        $previous  = new \RuntimeException('Previous error');
        $exception = new Timeout(
            'Test timeout',
            0,
            'job-123',
            15.5,
            $previous
        );

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Group('unit')]
    public function testConstructorWithDefaults() {
        $exception = new Timeout('Test timeout');

        $this->assertEquals('', $exception->getJobId());
        $this->assertEquals(0.0, $exception->getElapsedTime());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    #[Group('unit')]
    public function testExtendsRuntimeException() {
        $exception = new Timeout('Test timeout');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}
