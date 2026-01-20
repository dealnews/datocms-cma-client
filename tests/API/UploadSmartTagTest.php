<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\UploadSmartTag;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\UploadSmartTag class
 */
class UploadSmartTagTest extends TestCase {

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testList() {
        $expected_response = [
            'data' => [
                ['id' => 'person', 'type' => 'upload_smart_tag'],
                ['id' => 'outdoor', 'type' => 'upload_smart_tag'],
            ],
        ];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('GET', '/upload-smart-tags')
            ->willReturn($expected_response);

        $smart_tag = new UploadSmartTag($mock_handler);
        $result    = $smart_tag->list();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testListReturnsEmptyData() {
        $expected_response = ['data' => []];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('GET', '/upload-smart-tags')
            ->willReturn($expected_response);

        $smart_tag = new UploadSmartTag($mock_handler);
        $result    = $smart_tag->list();

        $this->assertEquals($expected_response, $result);
    }
}
