<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use DealNews\DatoCMS\CMA\Input\ModelFilter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Input\ModelFilter class
 */
class ModelFilterTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues(): void {
        $filter = new ModelFilter();

        $this->assertNull($filter->id);
        $this->assertEquals('item_type_filter', $filter->type);
        $this->assertEquals([], $filter->attributes);
        $this->assertEquals('', $filter->getItemTypeId());
    }

    #[Group('unit')]
    public function testConstructorWithItemTypeId(): void {
        $filter = new ModelFilter('model-123');

        $this->assertEquals('model-123', $filter->getItemTypeId());
    }

    #[Group('unit')]
    public function testSetItemType(): void {
        $filter = new ModelFilter();
        $result = $filter->setItemType('model-456');

        $this->assertEquals('model-456', $filter->getItemTypeId());
        $this->assertSame($filter, $result);
    }

    #[Group('unit')]
    public function testAttributesCanBeSet(): void {
        $filter                         = new ModelFilter();
        $filter->attributes['name']     = 'Draft posts';
        $filter->attributes['shared']   = true;
        $filter->attributes['order_by'] = '_updated_at_ASC';

        $this->assertEquals('Draft posts', $filter->attributes['name']);
        $this->assertTrue($filter->attributes['shared']);
        $this->assertEquals('_updated_at_ASC', $filter->attributes['order_by']);
    }

    #[Group('unit')]
    public function testFilterAttributeCanBeSet(): void {
        $filter                       = new ModelFilter();
        $filter->attributes['filter'] = [
            'query'  => 'foo bar',
            'fields' => [
                '_status' => ['eq' => 'draft'],
            ],
        ];

        $this->assertEquals('foo bar', $filter->attributes['filter']['query']);
        $this->assertEquals(['eq' => 'draft'], $filter->attributes['filter']['fields']['_status']);
    }

    #[Group('unit')]
    public function testColumnsAttributeCanBeSet(): void {
        $filter                        = new ModelFilter();
        $filter->attributes['columns'] = [
            ['name' => '_preview', 'width' => 0.6],
            ['name' => '_status', 'width' => 0.4],
        ];

        $this->assertCount(2, $filter->attributes['columns']);
        $this->assertEquals('_preview', $filter->attributes['columns'][0]['name']);
        $this->assertEquals(0.6, $filter->attributes['columns'][0]['width']);
    }

    #[Group('unit')]
    public function testIdCanBeSet(): void {
        $filter     = new ModelFilter();
        $filter->id = 'filter-123';

        $this->assertEquals('filter-123', $filter->id);
    }

    #[Group('unit')]
    public function testToArrayTypeProperlySet(): void {
        $filter                     = new ModelFilter('model-123');
        $filter->attributes['name'] = 'Test';

        $array = $filter->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertEquals('item_type_filter', $array['type']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyId(): void {
        $filter                     = new ModelFilter('model-123');
        $filter->attributes['name'] = 'Test';

        $array = $filter->toArray();

        $this->assertArrayNotHasKey('id', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesIdWhenSet(): void {
        $filter                     = new ModelFilter('model-123');
        $filter->id                 = 'filter-123';
        $filter->attributes['name'] = 'Test';

        $array = $filter->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertEquals('filter-123', $array['id']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyAttributes(): void {
        $filter = new ModelFilter('model-123');

        $array = $filter->toArray();

        $this->assertArrayNotHasKey('attributes', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesAttributesWhenSet(): void {
        $filter                       = new ModelFilter('model-123');
        $filter->attributes['name']   = 'Draft posts';
        $filter->attributes['shared'] = true;

        $array = $filter->toArray();

        $this->assertArrayHasKey('attributes', $array);
        $this->assertEquals('Draft posts', $array['attributes']['name']);
        $this->assertTrue($array['attributes']['shared']);
    }

    #[Group('unit')]
    public function testToArrayIncludesRelationshipsWhenItemTypeSet(): void {
        $filter                     = new ModelFilter('model-123');
        $filter->attributes['name'] = 'Test';

        $array = $filter->toArray();

        $this->assertArrayHasKey('relationships', $array);
        $this->assertArrayHasKey('item_type', $array['relationships']);
        $this->assertEquals(
            ['data' => ['type' => 'item_type', 'id' => 'model-123']],
            $array['relationships']['item_type']
        );
    }

    #[Group('unit')]
    public function testToArrayExcludesRelationshipsWhenItemTypeNotSet(): void {
        $filter                     = new ModelFilter();
        $filter->attributes['name'] = 'Test';

        $array = $filter->toArray();

        $this->assertArrayNotHasKey('relationships', $array);
    }

    #[Group('unit')]
    public function testToArrayDoesNotIncludeItemTypeAsTopLevelKey(): void {
        $filter = new ModelFilter('model-123');

        $array = $filter->toArray();

        $this->assertArrayNotHasKey('item_type', $array);
    }

    #[Group('unit')]
    public function testFullFilterSerialization(): void {
        $filter             = new ModelFilter('model-456');
        $filter->id         = 'filter-789';
        $filter->attributes = [
            'name'     => 'Published articles',
            'filter'   => [
                'query'  => 'article',
                'fields' => [
                    '_status' => ['eq' => 'published'],
                ],
            ],
            'columns'  => [
                ['name' => '_preview', 'width' => 0.5],
                ['name' => '_updated_at', 'width' => 0.5],
            ],
            'order_by' => '_created_at_DESC',
            'shared'   => true,
        ];

        $array = $filter->toArray();

        $this->assertEquals('filter-789', $array['id']);
        $this->assertEquals('item_type_filter', $array['type']);
        $this->assertEquals('Published articles', $array['attributes']['name']);
        $this->assertEquals('article', $array['attributes']['filter']['query']);
        $this->assertCount(2, $array['attributes']['columns']);
        $this->assertEquals('_created_at_DESC', $array['attributes']['order_by']);
        $this->assertTrue($array['attributes']['shared']);
        $this->assertEquals(
            ['data' => ['type' => 'item_type', 'id' => 'model-456']],
            $array['relationships']['item_type']
        );
    }

    #[Group('unit')]
    public function testSetItemTypeAfterConstruction(): void {
        $filter = new ModelFilter();
        $filter->setItemType('late-model-id');
        $filter->attributes['name'] = 'Test';

        $array = $filter->toArray();

        $this->assertEquals(
            ['data' => ['type' => 'item_type', 'id' => 'late-model-id']],
            $array['relationships']['item_type']
        );
    }
}
