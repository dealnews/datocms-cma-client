<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\API\UploadTag;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Parameters\Common as CommonParameter;

/**
 * Tests for the API\UploadTag class
 */
class UploadTagTest extends TestCase {

    /**
     * Creates an UploadTag API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return UploadTag
     */
    protected function createUploadTagWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): UploadTag {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new UploadTag($mock_handler);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testListWithoutParameters() {
        $expected_response = [
            'data' => [
                ['id' => 'tag-1', 'type' => 'upload_tag'],
                ['id' => 'tag-2', 'type' => 'upload_tag'],
            ],
        ];
        $tag = $this->createUploadTagWithMock('GET', '/upload-tags', [], [], $expected_response);

        $result = $tag->list();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testListWithParameters() {
        $params = $this->createMock(CommonParameter::class);
        $params->method('toArray')->willReturn(['page' => ['limit' => 10]]);

        $expected_response = ['data' => []];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('GET', '/upload-tags', ['page' => ['limit' => 10]])
            ->willReturn($expected_response);

        $tag = new UploadTag($mock_handler);
        $result = $tag->list($params);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve() {
        $expected_response = [
            'data' => [
                'id'         => 'tag-123',
                'type'       => 'upload_tag',
                'attributes' => ['name' => 'banner'],
            ],
        ];
        $tag = $this->createUploadTagWithMock('GET', '/upload-tags/tag-123', [], [], $expected_response);

        $result = $tag->retrieve('tag-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreate() {
        $expected_data = [
            'data' => [
                'type'       => 'upload_tag',
                'attributes' => [
                    'name' => 'featured',
                ],
            ],
        ];
        $expected_response = [
            'data' => [
                'id'         => 'new-tag-id',
                'type'       => 'upload_tag',
                'attributes' => ['name' => 'featured'],
            ],
        ];
        $tag = $this->createUploadTagWithMock('POST', '/upload-tags', [], $expected_data, $expected_response);

        $result = $tag->create('featured');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete() {
        $expected_response = ['data' => []];
        $tag = $this->createUploadTagWithMock('DELETE', '/upload-tags/tag-to-delete', [], [], $expected_response);

        $result = $tag->delete('tag-to-delete');

        $this->assertEquals($expected_response, $result);
    }
}
