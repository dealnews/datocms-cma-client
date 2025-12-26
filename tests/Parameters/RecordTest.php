<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Parameters\Record;
use DealNews\DatoCMS\CMA\Parameters\Parts\Filter;
use DealNews\DatoCMS\CMA\Parameters\Parts\OrderBy;
use DealNews\DatoCMS\CMA\Parameters\Parts\Page;

/**
 * Tests for the Record parameters class
 */
class RecordTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues() {
        $params = new Record();

        $this->assertFalse($params->nested);
        $this->assertEquals('published', $params->version);
        $this->assertNull($params->locale);
        $this->assertInstanceOf(OrderBy::class, $params->order_by);
        $this->assertInstanceOf(Filter::class, $params->filter);
        $this->assertInstanceOf(Page::class, $params->page);
    }

    #[Group('unit')]
    public function testVersionCanBeSetToPublished() {
        $params = new Record();
        $params->version = 'published';

        $this->assertEquals('published', $params->version);
    }

    #[Group('unit')]
    public function testVersionCanBeSetToCurrent() {
        $params = new Record();
        $params->version = 'current';

        $this->assertEquals('current', $params->version);
    }

    #[Group('unit')]
    public function testVersionThrowsExceptionForInvalidValue() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('version must be "published" or "current"');

        $params = new Record();
        $params->version = 'invalid';
    }

    #[Group('unit')]
    public function testNestedCanBeSetToTrue() {
        $params = new Record();
        $params->nested = true;

        $this->assertTrue($params->nested);
    }

    #[Group('unit')]
    public function testLocaleCanBeSet() {
        $params = new Record();
        $params->locale = 'en';

        $this->assertEquals('en', $params->locale);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyValues() {
        $params = new Record();

        $array = $params->toArray();

        $this->assertArrayNotHasKey('nested', $array);
        $this->assertArrayNotHasKey('locale', $array);
        $this->assertArrayNotHasKey('order_by', $array);
        $this->assertArrayNotHasKey('filter', $array);
        $this->assertArrayNotHasKey('page', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesVersion() {
        $params = new Record();

        $array = $params->toArray();

        $this->assertArrayHasKey('version', $array);
        $this->assertEquals('published', $array['version']);
    }

    #[Group('unit')]
    public function testToArrayIncludesNestedWhenTrue() {
        $params = new Record();
        $params->nested = true;

        $array = $params->toArray();

        $this->assertArrayHasKey('nested', $array);
        $this->assertTrue($array['nested']);
    }

    #[Group('unit')]
    public function testToArrayIncludesLocaleWhenSet() {
        $params = new Record();
        $params->locale = 'es';

        $array = $params->toArray();

        $this->assertArrayHasKey('locale', $array);
        $this->assertEquals('es', $array['locale']);
    }

    #[Group('unit')]
    public function testToArrayFormatsOrderByAsCommaSeparatedString() {
        $params = new Record();
        $params->order_by->addOrderByField('created_at', 'DESC');
        $params->order_by->addOrderByField('title', 'ASC');

        $array = $params->toArray();

        $this->assertArrayHasKey('order_by', $array);
        $this->assertEquals('created_at_DESC,title_ASC', $array['order_by']);
    }

    #[Group('unit')]
    public function testToArrayIncludesFilterWhenSet() {
        $params = new Record();
        $params->filter->type = ['article', 'page'];

        $array = $params->toArray();

        $this->assertArrayHasKey('filter', $array);
        $this->assertEquals('article,page', $array['filter']['type']);
    }

    #[Group('unit')]
    public function testToArrayIncludesPageWhenNonDefault() {
        $params = new Record();
        $params->page->limit = 50;
        $params->page->offset = 100;

        $array = $params->toArray();

        $this->assertArrayHasKey('page', $array);
        $this->assertEquals(50, $array['page']['limit']);
        $this->assertEquals(100, $array['page']['offset']);
    }

    #[Group('unit')]
    #[DataProvider('fullParametersProvider')]
    public function testFullParametersSerialization(array $settings, array $expected) {
        $params = new Record();

        foreach ($settings as $key => $value) {
            if ($key === 'order_by') {
                foreach ($value as $field => $direction) {
                    $params->order_by->addOrderByField($field, $direction);
                }
            } elseif ($key === 'filter_type') {
                $params->filter->type = $value;
            } elseif ($key === 'filter_ids') {
                $params->filter->ids = $value;
            } elseif ($key === 'filter_query') {
                $params->filter->query = $value;
            } elseif ($key === 'page_limit') {
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
            'version only' => [
                'settings' => [
                    'version' => 'current',
                ],
                'expected' => [
                    'version' => 'current',
                ],
            ],
            'nested and locale' => [
                'settings' => [
                    'nested' => true,
                    'locale' => 'fr',
                ],
                'expected' => [
                    'nested' => true,
                    'locale' => 'fr',
                    'version' => 'published',
                ],
            ],
            'with order_by' => [
                'settings' => [
                    'order_by' => [
                        'updated_at' => 'DESC',
                    ],
                ],
                'expected' => [
                    'order_by' => 'updated_at_DESC',
                    'version' => 'published',
                ],
            ],
            'with filter type' => [
                'settings' => [
                    'filter_type' => ['blog_post'],
                ],
                'expected' => [
                    'version' => 'published',
                    'filter' => [
                        'type' => 'blog_post',
                    ],
                ],
            ],
            'with pagination' => [
                'settings' => [
                    'page_limit' => 25,
                    'page_offset' => 50,
                ],
                'expected' => [
                    'version' => 'published',
                    'page' => [
                        'limit' => 25,
                        'offset' => 50,
                    ],
                ],
            ],
        ];
    }
}
