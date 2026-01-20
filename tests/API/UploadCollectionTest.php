<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\UploadCollection;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\UploadCollection as UploadCollectionInput;
use DealNews\DatoCMS\CMA\Parameters\UploadCollection as UploadCollectionParameter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\UploadCollection class
 */
class UploadCollectionTest extends TestCase {

    /**
     * Creates an UploadCollection API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return UploadCollection
     */
    protected function createUploadCollectionWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): UploadCollection {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new UploadCollection($mock_handler);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testListWithoutParameters() {
        $expected_response = [
            'data' => [
                ['id' => 'col-1', 'type' => 'upload_collection'],
                ['id' => 'col-2', 'type' => 'upload_collection'],
            ],
        ];
        $collection = $this->createUploadCollectionWithMock('GET', '/upload-collections', [], [], $expected_response);

        $result = $collection->list();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testListWithParameters() {
        $params              = new UploadCollectionParameter();
        $params->page->limit = 25;

        $expected_query    = $params->toArray();
        $expected_response = ['data' => []];
        $collection        = $this->createUploadCollectionWithMock('GET', '/upload-collections', $expected_query, [], $expected_response);

        $result = $collection->list($params);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // listAll() tests
    // =========================================================================

    #[Group('unit')]
    public function testListAllWithNullParameters() {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with(
                'GET',
                '/upload-collections',
                $this->callback(function ($query) {
                    return isset($query['page']['limit']) &&
                           $query['page']['limit'] === 500;
                }),
                []
            )
            ->willReturn(['data' => [['id' => '1'], ['id' => '2']]]);

        $collection = new UploadCollection($mock_handler);
        $result     = $collection->listAll();

        $this->assertEquals(
            ['data' => [['id' => '1'], ['id' => '2']]],
            $result
        );
    }

    #[Group('unit')]
    public function testListAllWithProvidedParameters() {
        $params               = new UploadCollectionParameter();
        $params->page->limit  = 100;
        $params->page->offset = 50;

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with(
                'GET',
                '/upload-collections',
                $this->callback(function ($query) {
                    return isset($query['page']['limit']) &&
                           $query['page']['limit'] === 500;
                }),
                []
            )
            ->willReturn(['data' => [['id' => '1']]]);

        $collection = new UploadCollection($mock_handler);
        $result     = $collection->listAll($params);

        $this->assertEquals(['data' => [['id' => '1']]], $result);
    }

    #[Group('unit')]
    public function testListAllSinglePage() {
        $first_page = array_fill(0, 250, ['id' => 'collection']);

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->willReturn(['data' => $first_page]);

        $collection = new UploadCollection($mock_handler);
        $result     = $collection->listAll();

        $this->assertCount(250, $result['data']);
    }

    #[Group('unit')]
    public function testListAllMultiplePages() {
        $first_page  = array_fill(0, 500, ['id' => 'page1']);
        $second_page = array_fill(0, 250, ['id' => 'page2']);

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->exactly(2))
            ->method('execute')
            ->willReturnOnConsecutiveCalls(
                ['data' => $first_page],
                ['data' => $second_page]
            );

        $collection = new UploadCollection($mock_handler);
        $result     = $collection->listAll();

        $this->assertCount(750, $result['data']);
    }

    #[Group('unit')]
    public function testListAllExactly500OnLastPage() {
        $first_page  = array_fill(0, 500, ['id' => 'page1']);
        $second_page = array_fill(0, 500, ['id' => 'page2']);
        $third_page  = [];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->exactly(3))
            ->method('execute')
            ->willReturnOnConsecutiveCalls(
                ['data' => $first_page],
                ['data' => $second_page],
                ['data' => $third_page]
            );

        $collection = new UploadCollection($mock_handler);
        $result     = $collection->listAll();

        $this->assertCount(1000, $result['data']);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve() {
        $expected_response = [
            'data' => [
                'id'         => 'col-123',
                'type'       => 'upload_collection',
                'attributes' => ['label' => 'Product Images'],
            ],
        ];
        $collection = $this->createUploadCollectionWithMock('GET', '/upload-collections/col-123', [], [], $expected_response);

        $result = $collection->retrieve('col-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArray() {
        $data = [
            'type'       => 'upload_collection',
            'attributes' => ['label' => 'New Collection'],
        ];
        $expected_response = [
            'data' => [
                'id'   => 'new-col-id',
                'type' => 'upload_collection',
            ],
        ];
        $collection = $this->createUploadCollectionWithMock('POST', '/upload-collections', [], ['data' => $data], $expected_response);

        $result = $collection->create($data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithInput() {
        $input                      = new UploadCollectionInput();
        $input->attributes['label'] = 'Input Collection';

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = [
            'data' => [
                'id'   => 'new-col-id',
                'type' => 'upload_collection',
            ],
        ];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('POST', '/upload-collections', [], $expected_data)
            ->willReturn($expected_response);

        $collection = new UploadCollection($mock_handler);
        $result     = $collection->create($input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // update() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateWithArray() {
        $data = [
            'type'       => 'upload_collection',
            'attributes' => ['label' => 'Updated Label'],
        ];
        $expected_response = [
            'data' => [
                'id'   => 'col-123',
                'type' => 'upload_collection',
            ],
        ];
        $collection = $this->createUploadCollectionWithMock('PUT', '/upload-collections/col-123', [], ['data' => $data], $expected_response);

        $result = $collection->update('col-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUpdateWithInput() {
        $input                      = new UploadCollectionInput();
        $input->attributes['label'] = 'Updated via Input';

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = [
            'data' => [
                'id'   => 'col-456',
                'type' => 'upload_collection',
            ],
        ];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('PUT', '/upload-collections/col-456', [], $expected_data)
            ->willReturn($expected_response);

        $collection = new UploadCollection($mock_handler);
        $result     = $collection->update('col-456', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete() {
        $expected_response = ['data' => []];
        $collection        = $this->createUploadCollectionWithMock('DELETE', '/upload-collections/col-to-delete', [], [], $expected_response);

        $result = $collection->delete('col-to-delete');

        $this->assertEquals($expected_response, $result);
    }
}
