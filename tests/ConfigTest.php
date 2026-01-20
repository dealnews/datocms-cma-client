<?php

namespace DealNews\DatoCMS\CMA\Tests;

use DealNews\DatoCMS\CMA\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Tests for the Config singleton class
 */
class ConfigTest extends TestCase {

    /**
     * Store original environment variable values
     */
    protected array $original_env = [];

    protected function setUp(): void {
        parent::setUp();

        // Store original environment values
        $this->original_env = [
            'DN_DATOCMS_API_TOKEN'   => getenv('DN_DATOCMS_API_TOKEN'),
            'DN_DATOCMS_ENVIRONMENT' => getenv('DN_DATOCMS_ENVIRONMENT'),
            'DN_DATOCMS_BASE_URL'    => getenv('DN_DATOCMS_BASE_URL'),
            'DN_DATOCMS_LOG_LEVEL'   => getenv('DN_DATOCMS_LOG_LEVEL'),
        ];

        // Clear environment variables
        putenv('DN_DATOCMS_API_TOKEN');
        putenv('DN_DATOCMS_ENVIRONMENT');
        putenv('DN_DATOCMS_BASE_URL');
        putenv('DN_DATOCMS_LOG_LEVEL');

        // Reset the singleton
        Config::reset();
    }

    protected function tearDown(): void {
        parent::tearDown();

        // Restore original environment values
        foreach ($this->original_env as $key => $value) {
            if ($value === false) {
                putenv($key);
            } else {
                putenv("$key=$value");
            }
        }

        // Reset the singleton
        Config::reset();
    }

    #[Group('unit')]
    public function testInitReturnsSingleton() {
        $config1 = Config::init();
        $config2 = Config::init();

        $this->assertSame($config1, $config2);
    }

    #[Group('unit')]
    public function testDefaultValues() {
        $config = Config::init();

        $this->assertNull($config->apiToken);
        $this->assertNull($config->environment);
        $this->assertNull($config->base_url);
        $this->assertNull($config->logger);
        $this->assertEquals(LogLevel::INFO, $config->log_level);
    }

    #[Group('unit')]
    public function testReadsApiTokenFromEnvironment() {
        putenv('DN_DATOCMS_API_TOKEN=test-token-123');
        Config::reset();

        $config = Config::init();

        $this->assertEquals('test-token-123', $config->apiToken);
    }

    #[Group('unit')]
    public function testReadsEnvironmentFromEnvironment() {
        putenv('DN_DATOCMS_ENVIRONMENT=sandbox');
        Config::reset();

        $config = Config::init();

        $this->assertEquals('sandbox', $config->environment);
    }

    #[Group('unit')]
    public function testReadsBaseUrlFromEnvironment() {
        putenv('DN_DATOCMS_BASE_URL=https://proxy.example.com');
        Config::reset();

        $config = Config::init();

        $this->assertEquals('https://proxy.example.com', $config->base_url);
    }

    #[Group('unit')]
    public function testReadsLogLevelFromEnvironment() {
        putenv('DN_DATOCMS_LOG_LEVEL=debug');
        Config::reset();

        $config = Config::init();

        $this->assertEquals('debug', $config->log_level);
    }

    #[Group('unit')]
    public function testSetApiToken() {
        $config           = Config::init();
        $config->apiToken = 'new-token';

        $this->assertEquals('new-token', $config->apiToken);
    }

    #[Group('unit')]
    public function testSetEnvironment() {
        $config              = Config::init();
        $config->environment = 'production';

        $this->assertEquals('production', $config->environment);
    }

    #[Group('unit')]
    public function testSetBaseUrl() {
        $config           = Config::init();
        $config->base_url = 'https://custom.api.com';

        $this->assertEquals('https://custom.api.com', $config->base_url);
    }

    #[Group('unit')]
    public function testSetLogLevel() {
        $config            = Config::init();
        $config->log_level = LogLevel::DEBUG;

        $this->assertEquals(LogLevel::DEBUG, $config->log_level);
    }

    #[Group('unit')]
    public function testSetLogger() {
        $logger = $this->createMock(LoggerInterface::class);

        $config         = Config::init();
        $config->logger = $logger;

        $this->assertSame($logger, $config->logger);
    }

    #[Group('unit')]
    public function testGetUnknownPropertyReturnsNull() {
        $config = Config::init();

        $this->assertNull($config->unknown_property);
    }

    #[Group('unit')]
    public function testSetUnknownPropertyIsIgnored() {
        $config                   = Config::init();
        $config->unknown_property = 'value';

        $this->assertNull($config->unknown_property);
    }

    #[Group('unit')]
    public function testResetClearsSingleton() {
        $config1           = Config::init();
        $config1->apiToken = 'token-1';

        Config::reset();

        $config2 = Config::init();

        $this->assertNotSame($config1, $config2);
        $this->assertNull($config2->apiToken);
    }

    #[Group('unit')]
    public function testAllEnvironmentVariablesRead() {
        putenv('DN_DATOCMS_API_TOKEN=my-token');
        putenv('DN_DATOCMS_ENVIRONMENT=staging');
        putenv('DN_DATOCMS_BASE_URL=https://staging.api.com');
        putenv('DN_DATOCMS_LOG_LEVEL=warning');
        Config::reset();

        $config = Config::init();

        $this->assertEquals('my-token', $config->apiToken);
        $this->assertEquals('staging', $config->environment);
        $this->assertEquals('https://staging.api.com', $config->base_url);
        $this->assertEquals('warning', $config->log_level);
    }
}
