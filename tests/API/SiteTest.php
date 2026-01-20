<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\Site;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\Site as SiteInput;
use DealNews\DatoCMS\CMA\Parameters\Site as SiteParameters;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\Site class
 */
class SiteTest extends TestCase {

    /**
     * Creates a Site API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return Site
     */
    protected function createSiteWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): Site {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new Site($mock_handler);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieveWithoutParameters(): void {
        $expected_response = ['data' => ['id' => '1', 'type' => 'site']];
        $site              = $this->createSiteWithMock('GET', '/site', [], [], $expected_response);

        $result = $site->retrieve();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testRetrieveWithParametersContainingInclude(): void {
        $params          = new SiteParameters();
        $params->include = ['item_types', 'account'];

        $expected_query    = ['include' => 'item_types,account'];
        $expected_response = ['data' => ['id' => '1', 'type' => 'site']];
        $site              = $this->createSiteWithMock('GET', '/site', $expected_query, [], $expected_response);

        $result = $site->retrieve($params);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // update() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateWithArray(): void {
        $data = [
            'type'       => 'site',
            'attributes' => [
                'no_index' => true,
            ],
        ];

        $expected_response = ['data' => ['id' => '1', 'type' => 'site']];
        $site              = $this->createSiteWithMock('PUT', '/site', [], ['data' => $data], $expected_response);

        $result = $site->update($data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUpdateWithSiteInput(): void {
        $input             = new SiteInput();
        $input->attributes = ['no_index' => true];

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => '1', 'type' => 'site']];
        $site              = $this->createSiteWithMock('PUT', '/site', [], $expected_data, $expected_response);

        $result = $site->update($input);

        $this->assertEquals($expected_response, $result);
    }
}
