<?php

namespace DealNews\DatoCMS\CMA\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Client;
use DealNews\DatoCMS\CMA\Config;
use DealNews\DatoCMS\CMA\API\Environment;
use DealNews\DatoCMS\CMA\API\FieldSet;
use DealNews\DatoCMS\CMA\API\Field;
use DealNews\DatoCMS\CMA\API\Job;
use DealNews\DatoCMS\CMA\API\Maintenance;
use DealNews\DatoCMS\CMA\API\Model;
use DealNews\DatoCMS\CMA\API\Record;
use DealNews\DatoCMS\CMA\API\RecordVersion;
use DealNews\DatoCMS\CMA\API\ScheduledUnpublishing;
use DealNews\DatoCMS\CMA\API\Site;
use DealNews\DatoCMS\CMA\API\Upload;
use DealNews\DatoCMS\CMA\API\UploadCollection;
use DealNews\DatoCMS\CMA\API\UploadRequest;
use DealNews\DatoCMS\CMA\API\UploadSmartTag;
use DealNews\DatoCMS\CMA\API\UploadTag;
use DealNews\DatoCMS\CMA\API\ScheduledPublication;
use DealNews\DatoCMS\CMA\API\Webhook;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Tests for the Client entry point class
 */
class ClientTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        Config::reset();
    }

    protected function tearDown(): void {
        parent::tearDown();
        Config::reset();
    }


    #[Group('unit')]
    public function testConstructorSetsApiToken() {
        $client = new Client('my-api-token');
        $config = Config::init();

        $this->assertEquals('my-api-token', $config->apiToken);
    }

    #[Group('unit')]
    public function testConstructorSetsEnvironment() {
        $client = new Client('token', 'sandbox');
        $config = Config::init();

        $this->assertEquals('sandbox', $config->environment);
    }

    #[Group('unit')]
    public function testConstructorSetsLogger() {
        $logger = $this->createMock(LoggerInterface::class);
        $client = new Client('token', null, $logger);
        $config = Config::init();

        $this->assertSame($logger, $config->logger);
    }

    #[Group('unit')]
    public function testConstructorSetsLogLevel() {
        $client = new Client('token', null, null, LogLevel::DEBUG);
        $config = Config::init();

        $this->assertEquals(LogLevel::DEBUG, $config->log_level);
    }

    #[Group('unit')]
    public function testConstructorSetsBaseUrl() {
        $client = new Client('token', null, null, LogLevel::INFO, 'https://proxy.example.com');
        $config = Config::init();

        $this->assertEquals('https://proxy.example.com', $config->base_url);
    }

    #[Group('unit')]
    public function testConstructorWithAllParameters() {
        $logger = $this->createMock(LoggerInterface::class);

        $client = new Client(
            'full-token',
            'production',
            $logger,
            LogLevel::WARNING,
            'https://custom.api.com'
        );

        $config = Config::init();

        $this->assertEquals('full-token', $config->apiToken);
        $this->assertEquals('production', $config->environment);
        $this->assertSame($logger, $config->logger);
        $this->assertEquals(LogLevel::WARNING, $config->log_level);
        $this->assertEquals('https://custom.api.com', $config->base_url);
    }

    #[Group('unit')]
    public function testConstructorWithNullParametersUsesConfigDefaults() {
        // Set values in config first
        $config = Config::init();
        $config->apiToken = 'existing-token';
        $config->environment = 'existing-env';

        // Create client with nulls - should preserve existing config
        $client = new Client();

        $this->assertEquals('existing-token', $config->apiToken);
        $this->assertEquals('existing-env', $config->environment);
    }


    #[Group('unit')]
    #[DataProvider('provideMagicMethods')]
    public function testGetMagicMethod(string $property, string $expected_class) {
        // Set values in config first
        $config = Config::init();
        $config->apiToken = 'existing-token';
        $config->environment = 'existing-env';

        $client = new Client();

        $class = $client->$property;
        $this->assertInstanceOf($expected_class, $class);
    }

    public static function provideMagicMethods(): array {
        return [
            ['record', Record::class],
            ['record_version', RecordVersion::class],
            ['model', Model::class],
            ['fieldset', FieldSet::class],
            ['site', Site::class],
            ['upload', Upload::class],
            ['upload_request', UploadRequest::class],
            ['upload_collection', UploadCollection::class],
            ['upload_tag', UploadTag::class],
            ['upload_smart_tag', UploadSmartTag::class],
            ['scheduled_unpublishing', ScheduledUnpublishing::class],
            ['scheduled_publication', ScheduledPublication::class],
            ['field', Field::class],
            ['environment', Environment::class],
            ['webhook', Webhook::class],
            ['job', Job::class],
            ['maintenance', Maintenance::class],
        ];
    }
}
