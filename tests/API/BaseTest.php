<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\Record;
use DealNews\DatoCMS\CMA\Config;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\Base abstract class (tested via Record subclass)
 */
class BaseTest extends TestCase {

    #[Group('unit')]
    public function testConstructorWithInjectedHandler() {
        $mock_handler = $this->createMock(Handler::class);

        $record = new Record($mock_handler);

        // Use reflection to verify the handler was set
        $reflection     = new \ReflectionClass($record);
        $property       = $reflection->getProperty('handler');
        $property->setAccessible(true);
        $actual_handler = $property->getValue($record);

        $this->assertSame($mock_handler, $actual_handler);
    }

    #[Group('unit')]
    public function testConstructorWithoutHandlerUsesConfig() {
        $config              = new Config();
        $config->apiToken    = 'test-api-token';
        $config->environment = 'test-environment';
        $config->base_url    = 'https://test.example.com';

        $record = new Record(null, $config);

        // Use reflection to verify a Handler was created
        $reflection     = new \ReflectionClass($record);
        $property       = $reflection->getProperty('handler');
        $property->setAccessible(true);
        $actual_handler = $property->getValue($record);

        $this->assertInstanceOf(Handler::class, $actual_handler);
    }

    #[Group('unit')]
    public function testConstructorCreatesHandlerWithConfigValues() {
        $config              = new Config();
        $config->apiToken    = 'my-secret-token';
        $config->environment = 'staging';

        $record = new Record(null, $config);

        // Verify the handler was created (we can't easily inspect its internal state
        // without more complex mocking, but we verify it's the correct type)
        $reflection     = new \ReflectionClass($record);
        $property       = $reflection->getProperty('handler');
        $property->setAccessible(true);
        $actual_handler = $property->getValue($record);

        $this->assertInstanceOf(Handler::class, $actual_handler);
    }

    #[Group('unit')]
    public function testConstructorWithNoArgumentsThrowsRuntimeException() {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Either a Handler or a Config instance must be provided.');

        new Record();
    }

    #[Group('unit')]
    public function testHandlerPropertyIsProtected() {
        $reflection = new \ReflectionClass(Record::class);
        $property   = $reflection->getProperty('handler');

        $this->assertTrue($property->isProtected());
    }
}
