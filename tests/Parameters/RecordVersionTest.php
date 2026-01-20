<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters;

use DealNews\DatoCMS\CMA\Parameters\Parts\Page;
use DealNews\DatoCMS\CMA\Parameters\RecordVersion;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the RecordVersion parameters class
 */
class RecordVersionTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues() {
        $params = new RecordVersion();

        $this->assertFalse($params->nested);
        $this->assertInstanceOf(Page::class, $params->page);
    }

    #[Group('unit')]
    public function testNestedCanBeSetToTrue() {
        $params         = new RecordVersion();
        $params->nested = true;

        $this->assertTrue($params->nested);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyValues() {
        $params = new RecordVersion();

        $array = $params->toArray();

        $this->assertArrayNotHasKey('nested', $array);
        $this->assertArrayNotHasKey('page', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesNestedWhenTrue() {
        $params         = new RecordVersion();
        $params->nested = true;

        $array = $params->toArray();

        $this->assertArrayHasKey('nested', $array);
        $this->assertTrue($array['nested']);
    }

    #[Group('unit')]
    public function testToArrayIncludesPageWhenSet() {
        $params               = new RecordVersion();
        $params->page->limit  = 10;
        $params->page->offset = 5;

        $array = $params->toArray();

        $this->assertArrayHasKey('page', $array);
        $this->assertEquals(10, $array['page']['limit']);
        $this->assertEquals(5, $array['page']['offset']);
    }

    #[Group('unit')]
    #[DataProvider('fullParametersProvider')]
    public function testFullParametersSerialization(array $settings, array $expected) {
        $params = new RecordVersion();

        foreach ($settings as $key => $value) {
            if ($key === 'page_limit') {
                $params->page->limit = $value;
            } elseif ($key === 'page_offset') {
                $params->page->offset = $value;
            } else {
                $params->$key = $value;
            }
        }

        $array = $params->toArray();

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $array);
            $this->assertEquals($value, $array[$key]);
        }
    }

    public static function fullParametersProvider(): array {
        return [
            'nested only' => [
                'settings' => [
                    'nested' => true,
                ],
                'expected' => [
                    'nested' => true,
                ],
            ],
            'nested with pagination' => [
                'settings' => [
                    'nested'      => true,
                    'page_limit'  => 50,
                    'page_offset' => 100,
                ],
                'expected' => [
                    'nested' => true,
                    'page'   => [
                        'limit'  => 50,
                        'offset' => 100,
                    ],
                ],
            ],
            'pagination only' => [
                'settings' => [
                    'page_limit'  => 25,
                    'page_offset' => 10,
                ],
                'expected' => [
                    'page' => [
                        'limit'  => 25,
                        'offset' => 10,
                    ],
                ],
            ],
        ];
    }
}
