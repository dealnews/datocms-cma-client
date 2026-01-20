<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters;

use DealNews\DatoCMS\CMA\Parameters\UploadTag;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Parameters\UploadTag class
 */
class UploadTagTest extends TestCase {

    #[Group('unit')]
    public function testToArrayWithDefaults() {
        $params = new UploadTag();
        $result = $params->toArray();

        $this->assertEquals([], $result);
    }

    #[Group('unit')]
    public function testToArrayWithPageParameters() {
        $params               = new UploadTag();
        $params->page->limit  = 25;
        $params->page->offset = 50;

        $result = $params->toArray();

        $expected = [
            'page' => [
                'limit'  => 25,
                'offset' => 50,
            ],
        ];

        $this->assertEquals($expected, $result);
    }
}
