<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters\Parts;

use DealNews\DatoCMS\CMA\Parameters\Parts\Page;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase {

    #[Group('unit')]
    #[DataProvider('pageProvider')]
    public function testPage(?int $offset, ?int $limit, array $expected) {
        $page = new Page();

        if (!is_null($offset)) {
            $page->offset = $offset;
        }
        if (!is_null($limit)) {
            $page->limit = $limit;
        }

        $this->assertEquals($expected, $page->toArray());
    }

    public static function pageProvider(): array {
        return [
            'Nothing set' => [
                'offset'   => null,
                'limit'    => null,
                'expected' => [],
            ],
            'Offset set to default' => [
                'offset'   => 0,
                'limit'    => null,
                'expected' => [],
            ],
            'Limit set to default' => [
                'offset'   => null,
                'limit'    => 15,
                'expected' => [],
            ],
            'Offset set to non-default' => [
                'offset'   => 1,
                'limit'    => null,
                'expected' => [
                    'offset' => 1,
                ],
            ],
            'Limit set to non-default' => [
                'offset'   => null,
                'limit'    => 16,
                'expected' => [
                    'limit' => 16,
                ],
            ],
            'Both set to non-default' => [
                'offset'   => 100,
                'limit'    => 200,
                'expected' => [
                    'offset' => 100,
                    'limit'  => 200,
                ],
            ],
        ];
    }
}
