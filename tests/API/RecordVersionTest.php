<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\API\RecordVersion;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Parameters\RecordVersion as RecordVersionParameter;

/**
 * Tests for the API\RecordVersion class
 */
class RecordVersionTest extends TestCase {

    /**
     * Creates a RecordVersion API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return RecordVersion
     */
    protected function createRecordVersionWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): RecordVersion {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new RecordVersion($mock_handler);
    }

    // =========================================================================
    // restore() tests
    // =========================================================================

    #[Group('unit')]
    public function testRestore() {
        $expected_response = ['data' => ['id' => 'job-123', 'type' => 'job']];
        $record_version = $this->createRecordVersionWithMock(
            'POST',
            '/versions/version-123/restore',
            [],
            [],
            $expected_response
        );

        $result = $record_version->restore('version-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testListWithoutParameters() {
        $expected_response = ['data' => [['id' => 'version-1'], ['id' => 'version-2']]];
        $record_version = $this->createRecordVersionWithMock(
            'GET',
            '/items/record-123/versions',
            [],
            [],
            $expected_response
        );

        $result = $record_version->list('record-123');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testListWithParameters() {
        $parameters = new RecordVersionParameter();
        $parameters->nested = true;
        $parameters->page->limit = 10;
        $parameters->page->offset = 5;

        $expected_response = ['data' => [['id' => 'version-3']]];
        $record_version = $this->createRecordVersionWithMock(
            'GET',
            '/items/record-456/versions',
            ['nested' => true, 'page' => ['limit' => 10, 'offset' => 5]],
            [],
            $expected_response
        );

        $result = $record_version->list('record-456', $parameters);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieveWithoutParameters() {
        $expected_response = ['data' => ['id' => 'version-123', 'type' => 'item_version']];
        $record_version = $this->createRecordVersionWithMock(
            'GET',
            '/versions/version-123',
            [],
            [],
            $expected_response
        );

        $result = $record_version->retrieve('version-123');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testRetrieveWithParameters() {
        $parameters = new RecordVersionParameter();
        $parameters->nested = true;

        $expected_response = ['data' => ['id' => 'version-456', 'type' => 'item_version']];
        $record_version = $this->createRecordVersionWithMock(
            'GET',
            '/versions/version-456',
            ['nested' => true],
            [],
            $expected_response
        );

        $result = $record_version->retrieve('version-456', $parameters);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testRetrieveRemovesPageParameter() {
        $parameters = new RecordVersionParameter();
        $parameters->nested = true;
        $parameters->page->limit = 10;
        $parameters->page->offset = 5;

        $expected_response = ['data' => ['id' => 'version-789', 'type' => 'item_version']];
        $record_version = $this->createRecordVersionWithMock(
            'GET',
            '/versions/version-789',
            ['nested' => true],
            [],
            $expected_response
        );

        $result = $record_version->retrieve('version-789', $parameters);

        $this->assertEquals($expected_response, $result);
    }
}
