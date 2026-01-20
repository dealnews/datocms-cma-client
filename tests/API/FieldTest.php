<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\Field;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\Field as FieldInput;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\Field class
 */
class FieldTest extends TestCase {

    /**
     * Creates a Field API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return Field
     */
    protected function createFieldWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): Field {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new Field($mock_handler);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArray(): void {
        $data = [
            'type'       => 'field',
            'attributes' => [
                'label'      => 'Title',
                'field_type' => 'string',
                'api_key'    => 'title',
            ],
        ];
        $expected_response = ['data' => ['id' => 'new-field-id', 'type' => 'field']];
        $field             = $this->createFieldWithMock('POST', '/item-types/test-model/fields', [], ['data' => $data], $expected_response);

        $result = $field->create('test-model', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithFieldInput(): void {
        $input                           = new FieldInput();
        $input->attributes['label']      = 'Title';
        $input->attributes['field_type'] = 'string';
        $input->attributes['api_key']    = 'title';

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'new-field-id', 'type' => 'field']];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('POST', '/item-types/test-model/fields', [], $expected_data)
            ->willReturn($expected_response);

        $field  = new Field($mock_handler);
        $result = $field->create('test-model', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // update() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateWithArray(): void {
        $data = [
            'type'       => 'field',
            'attributes' => ['label' => 'Updated Title'],
        ];
        $expected_response = ['data' => ['id' => 'field-123', 'type' => 'field']];
        $field             = $this->createFieldWithMock(
            'PUT',
            '/fields/field-123',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $field->update('field-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUpdateWithFieldInput(): void {
        $input                      = new FieldInput();
        $input->attributes['label'] = 'Updated Title';

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'field-123', 'type' => 'field']];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('PUT', '/fields/field-123', [], $expected_data)
            ->willReturn($expected_response);

        $field  = new Field($mock_handler);
        $result = $field->update('field-123', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testList(): void {
        $expected_response = ['data' => [['id' => 'field-1'], ['id' => 'field-2']]];
        $field             = $this->createFieldWithMock('GET', '/item-types/test-model/fields', [], [], $expected_response);

        $result = $field->list('test-model');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // referencing() tests
    // =========================================================================

    #[Group('unit')]
    public function testReferencing(): void {
        $expected_response = ['data' => [['id' => 'field-1'], ['id' => 'field-2']]];
        $field             = $this->createFieldWithMock('GET', '/item-types/test-model/fields/referencing', [], [], $expected_response);

        $result = $field->referencing('test-model');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve(): void {
        $expected_response = ['data' => ['id' => 'field-123', 'type' => 'field']];
        $field             = $this->createFieldWithMock(
            'GET',
            '/fields/field-123',
            [],
            [],
            $expected_response
        );

        $result = $field->retrieve('field-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete(): void {
        $expected_response = ['data' => ['id' => 'field-123', 'type' => 'field']];
        $field             = $this->createFieldWithMock('DELETE', '/fields/field-123', [], [], $expected_response);

        $result = $field->delete('field-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // duplicate() tests
    // =========================================================================

    #[Group('unit')]
    public function testDuplicate(): void {
        $expected_response = ['data' => ['id' => 'duplicated-field-id', 'type' => 'field']];
        $field             = $this->createFieldWithMock(
            'POST',
            '/fields/field-123/duplicate',
            [],
            [],
            $expected_response
        );

        $result = $field->duplicate('field-123');

        $this->assertEquals($expected_response, $result);
    }
}
