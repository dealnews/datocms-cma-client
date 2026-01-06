<?php

namespace DealNews\DatoCMS\CMA\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Client;
use DealNews\DatoCMS\CMA\Config;
use DealNews\DatoCMS\CMA\API\Model;
use DealNews\DatoCMS\CMA\API\Record;
use DealNews\DatoCMS\CMA\API\Upload;
use DealNews\DatoCMS\CMA\API\UploadCollection;
use DealNews\DatoCMS\CMA\API\UploadRequest;
use DealNews\DatoCMS\CMA\API\UploadSmartTag;
use DealNews\DatoCMS\CMA\API\UploadTag;
use DealNews\DatoCMS\CMA\API\ScheduledPublication;
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
    public function testConstructorCreatesRecordApi() {
        $client = new Client('test-token');

        $this->assertInstanceOf(Record::class, $client->record);
    }

    #[Group('unit')]
    public function testConstructorCreatesModelApi() {
        $client = new Client('test-token');

        $this->assertInstanceOf(Model::class, $client->model);
    }

    #[Group('unit')]
    public function testConstructorCreatesUploadApi() {
        $client = new Client('test-token');

        $this->assertInstanceOf(Upload::class, $client->upload);
    }

    #[Group('unit')]
    public function testConstructorCreatesUploadRequestApi() {
        $client = new Client('test-token');

        $this->assertInstanceOf(UploadRequest::class, $client->upload_request);
    }

    #[Group('unit')]
    public function testConstructorCreatesUploadCollectionApi() {
        $client = new Client('test-token');

        $this->assertInstanceOf(UploadCollection::class, $client->upload_collection);
    }

    #[Group('unit')]
    public function testConstructorCreatesUploadTagApi() {
        $client = new Client('test-token');

        $this->assertInstanceOf(UploadTag::class, $client->upload_tag);
    }

    #[Group('unit')]
    public function testConstructorCreatesUploadSmartTagApi() {
        $client = new Client('test-token');

        $this->assertInstanceOf(UploadSmartTag::class, $client->upload_smart_tag);
    }

    #[Group('unit')]
    public function testConstructorCreatesScheduledPublicationApi() {
        $client = new Client('test-token');

        $this->assertInstanceOf(ScheduledPublication::class, $client->scheduled_publication);
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
        $this->assertInstanceOf(Record::class, $client->record);
        $this->assertInstanceOf(Model::class, $client->model);
        $this->assertInstanceOf(Upload::class, $client->upload);
        $this->assertInstanceOf(UploadRequest::class, $client->upload_request);
        $this->assertInstanceOf(UploadCollection::class, $client->upload_collection);
        $this->assertInstanceOf(UploadTag::class, $client->upload_tag);
        $this->assertInstanceOf(UploadSmartTag::class, $client->upload_smart_tag);
        $this->assertInstanceOf(ScheduledPublication::class, $client->scheduled_publication);
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
    public function testRecordPropertyIsReadonly() {
        $client = new Client('token');

        $reflection = new \ReflectionProperty($client, 'record');

        $this->assertTrue($reflection->isReadOnly());
    }

    #[Group('unit')]
    public function testModelPropertyIsReadonly() {
        $client = new Client('token');

        $reflection = new \ReflectionProperty($client, 'model');

        $this->assertTrue($reflection->isReadOnly());
    }

    #[Group('unit')]
    public function testUploadPropertyIsReadonly() {
        $client = new Client('token');

        $reflection = new \ReflectionProperty($client, 'upload');

        $this->assertTrue($reflection->isReadOnly());
    }

    #[Group('unit')]
    public function testUploadRequestPropertyIsReadonly() {
        $client = new Client('token');

        $reflection = new \ReflectionProperty($client, 'upload_request');

        $this->assertTrue($reflection->isReadOnly());
    }

    #[Group('unit')]
    public function testUploadCollectionPropertyIsReadonly() {
        $client = new Client('token');

        $reflection = new \ReflectionProperty($client, 'upload_collection');

        $this->assertTrue($reflection->isReadOnly());
    }

    #[Group('unit')]
    public function testUploadTagPropertyIsReadonly() {
        $client = new Client('token');

        $reflection = new \ReflectionProperty($client, 'upload_tag');

        $this->assertTrue($reflection->isReadOnly());
    }

    #[Group('unit')]
    public function testUploadSmartTagPropertyIsReadonly() {
        $client = new Client('token');

        $reflection = new \ReflectionProperty($client, 'upload_smart_tag');

        $this->assertTrue($reflection->isReadOnly());
    }

    #[Group('unit')]
    public function testScheduledPublicationPropertyIsReadonly() {
        $client = new Client('token');

        $reflection = new \ReflectionProperty($client, 'scheduled_publication');

        $this->assertTrue($reflection->isReadOnly());
    }
}
