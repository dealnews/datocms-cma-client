<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\API\Model;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Parameters\Model as ModelParameter;
use DealNews\DatoCMS\CMA\Input\Model as ModelInput;

/**
 * Tests for the API\Model class
 */
class ModelTest extends TestCase {

    /**
     * Creates a Model API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return Model
     */
    protected function createModelWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): Model {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new Model($mock_handler);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testListWithoutParameters(): void {
        $expected_response = ['data' => [['id' => '1'], ['id' => '2']]];
        $model = $this->createModelWithMock('GET', '/item-types', [], [], $expected_response);

        $result = $model->list();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testListWithParameters(): void {
        $params = new ModelParameter();
        $params->page->limit = 50;
        $params->page->offset = 100;

        $expected_query = $params->toArray();
        $expected_response = ['data' => []];
        $model = $this->createModelWithMock('GET', '/item-types', $expected_query, [], $expected_response);

        $result = $model->list($params);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve(): void {
        $expected_response = ['data' => ['id' => 'model-123', 'type' => 'item_type']];
        $model = $this->createModelWithMock(
            'GET',
            '/item-types/model-123',
            [],
            [],
            $expected_response
        );

        $result = $model->retrieve('model-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArray(): void {
        $data = [
            'type' => 'item_type',
            'attributes' => [
                'name' => 'Blog Post',
                'api_key' => 'blog_post',
            ],
        ];
        $expected_response = ['data' => ['id' => 'new-id', 'type' => 'item_type']];
        $model = $this->createModelWithMock('POST', '/item-types', [], ['data' => $data], $expected_response);

        $result = $model->create($data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithModelInput(): void {
        $input = new ModelInput();
        $input->attributes['name'] = 'Blog Post';
        $input->attributes['api_key'] = 'blog_post';

        $expected_data = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'new-id', 'type' => 'item_type']];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('POST', '/item-types', [], $expected_data)
            ->willReturn($expected_response);

        $model = new Model($mock_handler);
        $result = $model->create($input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // update() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateWithArray(): void {
        $data = [
            'type' => 'item_type',
            'attributes' => ['name' => 'Updated Name'],
        ];
        $expected_response = ['data' => ['id' => 'model-123', 'type' => 'item_type']];
        $model = $this->createModelWithMock(
            'PUT',
            '/item-types/model-123',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $model->update('model-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUpdateWithModelInput(): void {
        $input = new ModelInput();
        $input->attributes['name'] = 'Updated Name';

        $expected_data = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'model-123', 'type' => 'item_type']];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('PUT', '/item-types/model-123', [], $expected_data)
            ->willReturn($expected_response);

        $model = new Model($mock_handler);
        $result = $model->update('model-123', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete(): void {
        $expected_response = ['data' => ['id' => 'model-123', 'type' => 'item_type']];
        $model = $this->createModelWithMock('DELETE', '/item-types/model-123', [], [], $expected_response);

        $result = $model->delete('model-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // duplicate() tests
    // =========================================================================

    #[Group('unit')]
    public function testDuplicate(): void {
        $expected_response = ['data' => ['id' => 'duplicated-id', 'type' => 'item_type']];
        $model = $this->createModelWithMock(
            'POST',
            '/item-types/original-id/duplicate',
            [],
            [],
            $expected_response
        );

        $result = $model->duplicate('original-id');

        $this->assertEquals($expected_response, $result);
    }
}
