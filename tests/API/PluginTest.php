<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\Plugin;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\Plugin as PluginInput;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\Plugin class
 */
#[Group('unit')]
class PluginTest extends TestCase {

    /**
     * Creates a Plugin API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return Plugin
     */
    protected function createPluginWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): Plugin {
        $mock = $this->createMock(Handler::class);
        $mock->expects($this->once())
             ->method('execute')
             ->with($expected_method, $expected_path, $expected_query, $expected_data)
             ->willReturn($return_value);

        return new Plugin($mock);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArray(): void {
        $data = [
            'type'       => 'plugin',
            'attributes' => [
                'package_name' => 'datocms-plugin-star-rating-editor',
            ],
        ];
        $expected_response = ['data' => ['id' => 'plugin-123', 'type' => 'plugin']];
        $plugin            = $this->createPluginWithMock(
            'POST',
            '/plugins',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $plugin->create($data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithPluginInput(): void {
        $input             = new PluginInput();
        $input->attributes = ['package_name' => 'datocms-plugin-star-rating-editor'];

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'plugin-456', 'type' => 'plugin']];
        $plugin            = $this->createPluginWithMock(
            'POST',
            '/plugins',
            [],
            $expected_data,
            $expected_response
        );

        $result = $plugin->create($input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // update() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateWithArray(): void {
        $data = [
            'type'       => 'plugin',
            'id'         => 'plugin-123',
            'attributes' => [
                'name' => 'Updated Plugin',
            ],
        ];
        $expected_response = ['data' => ['id' => 'plugin-123', 'type' => 'plugin']];
        $plugin            = $this->createPluginWithMock(
            'PUT',
            '/plugins/plugin-123',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $plugin->update('plugin-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUpdateWithPluginInput(): void {
        $input             = new PluginInput();
        $input->id         = 'plugin-123';
        $input->attributes = ['name' => 'Updated Plugin'];

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'plugin-123', 'type' => 'plugin']];
        $plugin            = $this->createPluginWithMock(
            'PUT',
            '/plugins/plugin-123',
            [],
            $expected_data,
            $expected_response
        );

        $result = $plugin->update('plugin-123', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testList(): void {
        $expected_response = ['data' => [['id' => 'plugin-1'], ['id' => 'plugin-2']]];
        $plugin            = $this->createPluginWithMock(
            'GET',
            '/plugins',
            [],
            [],
            $expected_response
        );

        $result = $plugin->list();

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve(): void {
        $expected_response = ['data' => ['id' => 'plugin-123', 'type' => 'plugin']];
        $plugin            = $this->createPluginWithMock(
            'GET',
            '/plugins/plugin-123',
            [],
            [],
            $expected_response
        );

        $result = $plugin->retrieve('plugin-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete(): void {
        $expected_response = ['data' => ['id' => 'plugin-123', 'type' => 'plugin']];
        $plugin            = $this->createPluginWithMock(
            'DELETE',
            '/plugins/plugin-123',
            [],
            [],
            $expected_response
        );

        $result = $plugin->delete('plugin-123');

        $this->assertEquals($expected_response, $result);
    }
}
