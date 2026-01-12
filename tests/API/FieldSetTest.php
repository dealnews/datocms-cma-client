<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\API\FieldSet;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\FieldSet as FieldSetInput;

/**
 * Tests for the API\FieldSet class
 */
class FieldSetTest extends TestCase {

    /**
     * Creates a FieldSet API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return FieldSet
     */
    protected function createFieldSetWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): FieldSet {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new FieldSet($mock_handler);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArray(): void {
        $data = [
            'type' => 'fieldset',
            'attributes' => [
                'title' => 'Contact Information',
                'collapsible' => true,
            ],
        ];
        $expected_response = ['data' => ['id' => 'new-fieldset-id', 'type' => 'fieldset']];
        $fieldset = $this->createFieldSetWithMock(
            'POST',
            '/item-types/model-123/fieldsets',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $fieldset->create('model-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithFieldSetInput(): void {
        $input = new FieldSetInput();
        $input->attributes['title'] = 'Contact Information';
        $input->attributes['collapsible'] = true;

        $expected_data = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'new-fieldset-id', 'type' => 'fieldset']];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('POST', '/item-types/model-123/fieldsets', [], $expected_data)
            ->willReturn($expected_response);

        $fieldset = new FieldSet($mock_handler);
        $result = $fieldset->create('model-123', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // update() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateWithArray(): void {
        $data = [
            'type' => 'fieldset',
            'attributes' => ['title' => 'Updated Title'],
        ];
        $expected_response = ['data' => ['id' => 'fieldset-123', 'type' => 'fieldset']];
        $fieldset = $this->createFieldSetWithMock(
            'PUT',
            '/fieldsets/fieldset-123',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $fieldset->update('fieldset-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUpdateWithFieldSetInput(): void {
        $input = new FieldSetInput();
        $input->attributes['title'] = 'Updated Title';

        $expected_data = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'fieldset-123', 'type' => 'fieldset']];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('PUT', '/fieldsets/fieldset-123', [], $expected_data)
            ->willReturn($expected_response);

        $fieldset = new FieldSet($mock_handler);
        $result = $fieldset->update('fieldset-123', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testList(): void {
        $expected_response = ['data' => [['id' => '1'], ['id' => '2']]];
        $fieldset = $this->createFieldSetWithMock(
            'GET',
            '/item-types/model-123/fieldsets',
            [],
            [],
            $expected_response
        );

        $result = $fieldset->list('model-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve(): void {
        $expected_response = ['data' => ['id' => 'fieldset-123', 'type' => 'fieldset']];
        $fieldset = $this->createFieldSetWithMock(
            'GET',
            '/fieldsets/fieldset-123',
            [],
            [],
            $expected_response
        );

        $result = $fieldset->retrieve('fieldset-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete(): void {
        $expected_response = ['data' => ['id' => 'fieldset-123', 'type' => 'fieldset']];
        $fieldset = $this->createFieldSetWithMock(
            'DELETE',
            '/fieldsets/fieldset-123',
            [],
            [],
            $expected_response
        );

        $result = $fieldset->delete('fieldset-123');

        $this->assertEquals($expected_response, $result);
    }
}
