<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters\Parts;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Parameters\Parts\OrderBy;

class OrderByTest extends TestCase {

    #[Group('unit')]
    #[DataProvider('orderByProvider')]
    public function testOrderBy(array $order_by, array $expected, ?string $expected_exception) {
        $o = new OrderBy();

        if (!empty($expected_exception)) {
            $this->expectException($expected_exception);
        }

        foreach ($order_by as $field => $direction) {
            $o->addOrderByField($field, $direction);
        }

        $this->assertEquals($expected, $o->toArray());
    }

    public static function orderByProvider(): array {
        return [
            'Valid field and direction' => [
                'order_by' => [
                    'foo' => 'DESC',
                ],
                'expected' => [
                    'foo_DESC',
                ],
                'expected_exception' => null,
            ],
            'Valid field and opposite direction' => [
                'order_by' => [
                    'foo' => 'ASC',
                ],
                'expected' => [
                    'foo_ASC',
                ],
                'expected_exception' => null,
            ],
            'Field with invalid direction' => [
                'order_by' => [
                    'foo' => 'up',
                ],
                'expected' => [],
                'expected_exception' => \InvalidArgumentException::class,
            ],
            'Multiple valid fields and directions' => [
                'order_by' => [
                    'foo' => 'DESC',
                    'bar' => 'ASC',
                    'baz' => 'DESC',
                ],
                'expected' => [
                    'foo_DESC',
                    'bar_ASC',
                    'baz_DESC',
                ],
                'expected_exception' => null,
            ],
        ];
    }

}