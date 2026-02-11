<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\ModelFilter;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\ModelFilter as ModelFilterInput;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\ModelFilter class
 */
class ModelFilterTest extends TestCase {

    /**
     * Creates a ModelFilter API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return ModelFilter
     */
    protected function createModelFilterWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): ModelFilter {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new ModelFilter($mock_handler);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testList(): void {
        $expected_response = ['data' => [['id' => '1'], ['id' => '2']]];
        $filter            = $this->createModelFilterWithMock(
            'GET',
            '/item-type-filters',
            [],
            [],
            $expected_response
        );

        $result = $filter->list();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testListReturnsEmptyArray(): void {
        $expected_response = ['data' => []];
        $filter            = $this->createModelFilterWithMock(
            'GET',
            '/item-type-filters',
            [],
            [],
            $expected_response
        );

        $result = $filter->list();

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve(): void {
        $expected_response = [
            'data' => [
                'id'   => 'filter-123',
                'type' => 'item_type_filter',
            ],
        ];
        $filter = $this->createModelFilterWithMock(
            'GET',
            '/item-type-filters/filter-123',
            [],
            [],
            $expected_response
        );

        $result = $filter->retrieve('filter-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArray(): void {
        $data = [
            'type'          => 'item_type_filter',
            'attributes'    => [
                'name' => 'Draft posts',
            ],
            'relationships' => [
                'item_type' => [
                    'data' => ['type' => 'item_type', 'id' => 'model-123'],
                ],
            ],
        ];
        $expected_response = [
            'data' => [
                'id'   => 'new-filter-id',
                'type' => 'item_type_filter',
            ],
        ];
        $filter = $this->createModelFilterWithMock(
            'POST',
            '/item-type-filters',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $filter->create($data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithModelFilterInput(): void {
        $input                     = new ModelFilterInput('model-123');
        $input->attributes['name'] = 'Draft posts';

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = [
            'data' => [
                'id'   => 'new-filter-id',
                'type' => 'item_type_filter',
            ],
        ];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('POST', '/item-type-filters', [], $expected_data)
            ->willReturn($expected_response);

        $filter = new ModelFilter($mock_handler);
        $result = $filter->create($input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // update() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateWithArray(): void {
        $data = [
            'type'       => 'item_type_filter',
            'attributes' => ['name' => 'Updated Name'],
        ];
        $expected_response = [
            'data' => [
                'id'   => 'filter-123',
                'type' => 'item_type_filter',
            ],
        ];
        $filter = $this->createModelFilterWithMock(
            'PUT',
            '/item-type-filters/filter-123',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $filter->update('filter-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUpdateWithModelFilterInput(): void {
        $input                     = new ModelFilterInput();
        $input->attributes['name'] = 'Updated Name';

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = [
            'data' => [
                'id'   => 'filter-123',
                'type' => 'item_type_filter',
            ],
        ];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('PUT', '/item-type-filters/filter-123', [], $expected_data)
            ->willReturn($expected_response);

        $filter = new ModelFilter($mock_handler);
        $result = $filter->update('filter-123', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete(): void {
        $expected_response = [
            'data' => [
                'id'   => 'filter-123',
                'type' => 'item_type_filter',
            ],
        ];
        $filter = $this->createModelFilterWithMock(
            'DELETE',
            '/item-type-filters/filter-123',
            [],
            [],
            $expected_response
        );

        $result = $filter->delete('filter-123');

        $this->assertEquals($expected_response, $result);
    }
}
