<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters\Parts;

use DealNews\DatoCMS\CMA\Parameters\Parts\Filter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {

    #[Group('unit')]
    #[DataProvider('filterProvider')]
    public function testFilter(array $properties, array $expected) {
        $filter = new Filter();
        foreach ($properties as $property => $value) {
            if ($property === 'fields') {
                foreach ($value as $field_name => $field_op_and_value) {
                    foreach ($field_op_and_value as $field_op => $field_value) {
                        $filter->fields->addField($field_name, $field_value, $field_op);
                    }
                }
            } else {
                $filter->{$property} = $value;
            }
        }

        $this->assertEquals($expected, $filter->toArray());
    }


    public static function filterProvider(): array {
        return [
            'Apply ids' => [
                'properties' => [
                    'ids' => [
                        'abcd',
                        'efgh',
                    ],
                ],
                'expected' => [
                    'ids' => 'abcd,efgh',
                ],
            ],
            'Apply type' => [
                'properties' => [
                    'type' => [
                        'abcd',
                        'efgh',
                    ],
                ],
                'expected' => [
                    'type' => 'abcd,efgh',
                ],
            ],
            'Apply query' => [
                'properties' => [
                    'query' => 'hello world',
                ],
                'expected' => [
                    'query' => 'hello world',
                ],
            ],
            'Apply only_valid' => [
                'properties' => [
                    'only_valid' => true,
                ],
                'expected' => [
                    'only_valid' => true,
                ],
            ],
            'Apply fields' => [
                'properties' => [
                    'fields' => [
                        'foo' => [
                            'eq' => 'bar',
                        ],
                        'date' => [
                            'gt' => '2010-10-01',
                            'lt' => '2010-12-31',
                        ],
                    ],
                ],
                'expected' => [
                    'fields' => [
                        'foo' => [
                            'eq' => 'bar',
                        ],
                        'date' => [
                            'gt' => '2010-10-01',
                            'lt' => '2010-12-31',
                        ],
                    ],
                ],
            ],
            'Apply everything' => [
                'properties' => [
                    'ids' => [
                        'abcd',
                        'efgh',
                    ],
                    'type' => [
                        'type_1',
                        'type_2',
                    ],
                    'query'      => 'hello world',
                    'only_valid' => true,
                    'fields'     => [
                        'foo' => [
                            'eq' => 'bar',
                        ],
                        'date' => [
                            'gt' => '2010-10-01',
                            'lt' => '2010-12-31',
                        ],
                    ],
                ],
                'expected' => [
                    'ids'        => 'abcd,efgh',
                    'type'       => 'type_1,type_2',
                    'query'      => 'hello world',
                    'only_valid' => true,
                    'fields'     => [
                        'foo' => [
                            'eq' => 'bar',
                        ],
                        'date' => [
                            'gt' => '2010-10-01',
                            'lt' => '2010-12-31',
                        ],
                    ],
                ],
            ],
        ];
    }

}
