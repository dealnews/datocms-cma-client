<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\API\Record;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Parameters\Record as RecordParameter;
use DealNews\DatoCMS\CMA\Input\Record as RecordInput;

/**
 * Tests for the API\Record class
 */
class RecordTest extends TestCase {

    /**
     * Creates a Record API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return Record
     */
    protected function createRecordWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): Record {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new Record($mock_handler);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testListWithoutParameters() {
        $expected_response = ['data' => [['id' => '1'], ['id' => '2']]];
        $record = $this->createRecordWithMock('GET', '/items', [], [], $expected_response);

        $result = $record->list();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testListWithParameters() {
        $params = new RecordParameter();
        $params->version = 'published';
        $params->nested = true;

        $expected_query = $params->toArray();
        $expected_response = ['data' => []];
        $record = $this->createRecordWithMock('GET', '/items', $expected_query, [], $expected_response);

        $result = $record->list($params);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArray() {
        $data = [
            'type' => 'item',
            'attributes' => ['title' => 'Test'],
            'relationships' => ['item_type' => ['data' => ['type' => 'item_type', 'id' => 'model-123']]],
        ];
        $expected_response = ['data' => ['id' => 'new-id', 'type' => 'item']];
        $record = $this->createRecordWithMock('POST', '/items', [], ['data' => $data], $expected_response);

        $result = $record->create($data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithRecordInput() {
        $input = new RecordInput('model-123');
        $input->attributes['title'] = 'Test Record';

        $expected_data = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'new-id', 'type' => 'item']];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('POST', '/items', [], $expected_data)
            ->willReturn($expected_response);

        $record = new Record($mock_handler);
        $result = $record->create($input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // duplicate() tests
    // =========================================================================

    #[Group('unit')]
    public function testDuplicate() {
        $expected_response = ['data' => ['id' => 'duplicated-id', 'type' => 'item']];
        $record = $this->createRecordWithMock('POST', '/items/original-id/duplicate', [], [], $expected_response);

        $result = $record->duplicate('original-id');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // update() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateWithArray() {
        $data = [
            'type' => 'item',
            'attributes' => ['title' => 'Updated Title'],
        ];
        $expected_response = ['data' => ['id' => 'record-123', 'type' => 'item']];
        $record = $this->createRecordWithMock('PUT', '/items/record-123', [], ['data' => $data], $expected_response);

        $result = $record->update('record-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUpdateWithRecordInput() {
        $input = new RecordInput('model-123');
        $input->attributes['title'] = 'Updated Title';

        $expected_data = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'record-123', 'type' => 'item']];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('PUT', '/items/record-123', [], $expected_data)
            ->willReturn($expected_response);

        $record = new Record($mock_handler);
        $result = $record->update('record-123', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // references() tests
    // =========================================================================

    #[Group('unit')]
    public function testReferencesWithDefaults() {
        $expected_response = ['data' => []];
        $record = $this->createRecordWithMock('GET', '/items/record-123/references', [], [], $expected_response);

        $result = $record->references('record-123');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testReferencesWithNested() {
        $expected_response = ['data' => []];
        $record = $this->createRecordWithMock(
            'GET',
            '/items/record-123/references',
            ['nested' => true],
            [],
            $expected_response
        );

        $result = $record->references('record-123', true);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testReferencesWithPublishedVersion() {
        $expected_response = ['data' => []];
        $record = $this->createRecordWithMock(
            'GET',
            '/items/record-123/references',
            ['version' => 'published'],
            [],
            $expected_response
        );

        $result = $record->references('record-123', false, 'published');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testReferencesWithCurrentVersion() {
        $expected_response = ['data' => []];
        $record = $this->createRecordWithMock(
            'GET',
            '/items/record-123/references',
            ['version' => 'current'],
            [],
            $expected_response
        );

        $result = $record->references('record-123', false, 'current');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testReferencesThrowsOnInvalidVersion() {
        $mock_handler = $this->createMock(Handler::class);
        $record = new Record($mock_handler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('version must be "published" or "current"');

        $record->references('record-123', false, 'invalid');
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieveWithDefaults() {
        $expected_response = ['data' => ['id' => 'record-123', 'type' => 'item']];
        $record = $this->createRecordWithMock(
            'GET',
            '/items/record-123',
            ['version' => 'current'],
            [],
            $expected_response
        );

        $result = $record->retrieve('record-123');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testRetrieveWithNested() {
        $expected_response = ['data' => ['id' => 'record-123']];
        $record = $this->createRecordWithMock(
            'GET',
            '/items/record-123',
            ['version' => 'current', 'nested' => true],
            [],
            $expected_response
        );

        $result = $record->retrieve('record-123', true);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testRetrieveWithPublishedVersion() {
        $expected_response = ['data' => ['id' => 'record-123']];
        $record = $this->createRecordWithMock(
            'GET',
            '/items/record-123',
            ['version' => 'published'],
            [],
            $expected_response
        );

        $result = $record->retrieve('record-123', false, 'published');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testRetrieveThrowsOnInvalidVersion() {
        $mock_handler = $this->createMock(Handler::class);
        $record = new Record($mock_handler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('version must be "published" or "current"');

        $record->retrieve('record-123', false, 'invalid');
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete() {
        $expected_response = ['data' => ['id' => 'job-123', 'type' => 'job']];
        $record = $this->createRecordWithMock('DELETE', '/items/record-123', [], [], $expected_response);

        $result = $record->delete('record-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // publish() tests
    // =========================================================================

    #[Group('unit')]
    public function testPublishWithDefaults() {
        $expected_response = ['data' => ['id' => 'record-123']];
        $record = $this->createRecordWithMock(
            'PUT',
            '/items/record-123/publish',
            [],
            [],
            $expected_response
        );

        $result = $record->publish('record-123');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testPublishWithRecursive() {
        $expected_response = ['data' => ['id' => 'record-123']];
        $record = $this->createRecordWithMock(
            'PUT',
            '/items/record-123/publish',
            ['recursive' => true],
            [],
            $expected_response
        );

        $result = $record->publish('record-123', true);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testPublishWithSelectivePublishing() {
        $expected_data = [
            'data' => [
                'type' => 'selective_publish_operation',
                'attributes' => [
                    'content_in_locales' => ['en', 'es'],
                    'non_localized_content' => true,
                ],
            ]
        ];
        $expected_response = ['data' => ['id' => 'record-123']];
        $record = $this->createRecordWithMock(
            'PUT',
            '/items/record-123/publish',
            [],
            $expected_data,
            $expected_response
        );

        $result = $record->publish('record-123', false, ['en', 'es'], true);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testPublishThrowsWhenOnlyLocalesProvided() {
        $mock_handler = $this->createMock(Handler::class);
        $record = new Record($mock_handler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('both locales and non_localized_content must be set');

        $record->publish('record-123', false, ['en'], null);
    }

    #[Group('unit')]
    public function testPublishThrowsWhenOnlyNonLocalizedContentProvided() {
        $mock_handler = $this->createMock(Handler::class);
        $record = new Record($mock_handler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('both locales and non_localized_content must be set');

        $record->publish('record-123', false, null, true);
    }

    // =========================================================================
    // unpublish() tests
    // =========================================================================

    #[Group('unit')]
    public function testUnpublishWithDefaults() {
        $expected_response = ['data' => ['id' => 'record-123']];
        $record = $this->createRecordWithMock(
            'PUT',
            '/items/record-123/unpublish',
            [],
            [],
            $expected_response
        );

        $result = $record->unpublish('record-123');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUnpublishWithRecursive() {
        $expected_response = ['data' => ['id' => 'record-123']];
        $record = $this->createRecordWithMock(
            'PUT',
            '/items/record-123/unpublish',
            ['recursive' => true],
            [],
            $expected_response
        );

        $result = $record->unpublish('record-123', true);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUnpublishWithSelectiveUnpublishing() {
        $expected_data = [
            'data' => [
                'type' => 'selective_unpublish_operation',
                'attributes' => [
                    'content_in_locales' => ['fr'],
                ],
            ]
        ];
        $expected_response = ['data' => ['id' => 'record-123']];
        $record = $this->createRecordWithMock(
            'PUT',
            '/items/record-123/unpublish',
            [],
            $expected_data,
            $expected_response
        );

        $result = $record->unpublish('record-123', false, ['fr']);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // publishBulk() tests
    // =========================================================================

    #[Group('unit')]
    public function testPublishBulk() {
        $expected_data = [
            'data' => [
                'type' => 'item_bulk_publish_operation',
                'relationships' => [
                    'items' => [
                        'data' => [
                            ['type' => 'item', 'id' => 'id-1'],
                            ['type' => 'item', 'id' => 'id-2'],
                            ['type' => 'item', 'id' => 'id-3'],
                        ],
                    ]
                ],
            ]
        ];
        $expected_response = ['data' => ['id' => 'job-123', 'type' => 'job']];
        $record = $this->createRecordWithMock(
            'POST',
            '/items/bulk/publish',
            [],
            $expected_data,
            $expected_response
        );

        $result = $record->publishBulk(['id-1', 'id-2', 'id-3']);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // unpublishBulk() tests
    // =========================================================================

    #[Group('unit')]
    public function testUnpublishBulk() {
        $expected_data = [
            'data' => [
                'type' => 'item_bulk_unpublish_operation',
                'relationships' => [
                    'items' => [
                        'data' => [
                            ['type' => 'item', 'id' => 'id-1'],
                            ['type' => 'item', 'id' => 'id-2'],
                        ],
                    ]
                ],
            ]
        ];
        $expected_response = ['data' => ['id' => 'job-456', 'type' => 'job']];
        $record = $this->createRecordWithMock(
            'POST',
            '/items/bulk/unpublish',
            [],
            $expected_data,
            $expected_response
        );

        $result = $record->unpublishBulk(['id-1', 'id-2']);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // deleteBulk() tests
    // =========================================================================

    #[Group('unit')]
    public function testDeleteBulk() {
        $expected_data = [
            'data' => [
                'type' => 'item_bulk_destroy_operation',
                'relationships' => [
                    'items' => [
                        'data' => [
                            ['type' => 'item', 'id' => 'id-1'],
                        ],
                    ]
                ],
            ]
        ];
        $expected_response = ['data' => ['id' => 'job-789', 'type' => 'job']];
        $record = $this->createRecordWithMock(
            'POST',
            '/items/bulk/destroy',
            [],
            $expected_data,
            $expected_response
        );

        $result = $record->deleteBulk(['id-1']);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // moveToStageBulk() tests
    // =========================================================================

    #[Group('unit')]
    public function testMoveToStageBulk() {
        $expected_data = [
            'data' => [
                'type' => 'item_bulk_move_to_stage_operation',
                'attributes' => [
                    'stage' => 'review',
                ],
                'relationships' => [
                    'items' => [
                        'data' => [
                            ['type' => 'item', 'id' => 'id-1'],
                            ['type' => 'item', 'id' => 'id-2'],
                        ],
                    ]
                ],
            ]
        ];
        $expected_response = ['data' => ['id' => 'job-999', 'type' => 'job']];
        $record = $this->createRecordWithMock(
            'POST',
            '/items/bulk/move-to-stage',
            [],
            $expected_data,
            $expected_response
        );

        $result = $record->moveToStageBulk(['id-1', 'id-2'], 'review');

        $this->assertEquals($expected_response, $result);
    }
}
