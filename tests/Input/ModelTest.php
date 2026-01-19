<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Model;

/**
 * Tests for the Input\Model class
 */
class ModelTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues(): void {
        $model = new Model();

        $this->assertNull($model->id);
        $this->assertEquals('item_type', $model->type);
        $this->assertEquals([], $model->attributes);
    }

    #[Group('unit')]
    public function testTypeCanBeSetToItemType(): void {
        $model = new Model();

        $this->assertEquals('item_type', $model->type);
    }

    #[Group('unit')]
    public function testAttributesCanBeSet(): void {
        $model = new Model();
        $model->attributes['name'] = 'Blog Post';
        $model->attributes['api_key'] = 'blog_post';
        $model->attributes['singleton'] = false;

        $this->assertEquals('Blog Post', $model->attributes['name']);
        $this->assertEquals('blog_post', $model->attributes['api_key']);
        $this->assertFalse($model->attributes['singleton']);
    }

    #[Group('unit')]
    public function testIdCanBeSet(): void {
        $model = new Model();
        $model->id = 'model-123';

        $this->assertEquals('model-123', $model->id);
    }

    #[Group('unit')]
    public function testToArrayTypeProperlySet(): void {
        $model = new Model();
        $model->attributes['name'] = 'Test';

        $array = $model->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertEquals('item_type', $array['type']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyId(): void {
        $model = new Model();
        $model->attributes['name'] = 'Test';

        $array = $model->toArray();

        $this->assertArrayNotHasKey('id', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesIdWhenSet(): void {
        $model = new Model();
        $model->id = 'model-123';
        $model->attributes['name'] = 'Test';

        $array = $model->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertEquals('model-123', $array['id']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyAttributes(): void {
        $model = new Model();

        $array = $model->toArray();

        $this->assertArrayNotHasKey('attributes', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesAttributesWhenSet(): void {
        $model = new Model();
        $model->attributes['name'] = 'Blog Post';
        $model->attributes['api_key'] = 'blog_post';

        $array = $model->toArray();

        $this->assertArrayHasKey('attributes', $array);
        $this->assertEquals('Blog Post', $array['attributes']['name']);
        $this->assertEquals('blog_post', $array['attributes']['api_key']);
    }

    #[Group('unit')]
    public function testToArrayIncludesType(): void {
        $model = new Model();

        $array = $model->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertEquals('item_type', $array['type']);
    }

    #[Group('unit')]
    public function testFullModelSerialization(): void {
        $model = new Model();
        $model->id = 'model-456';
        $model->attributes = [
            'name' => 'Product',
            'api_key' => 'product',
            'singleton' => false,
            'sortable' => true,
            'modular_block' => false,
            'tree' => false,
            'draft_mode_active' => true,
        ];

        $array = $model->toArray();

        $this->assertEquals('model-456', $array['id']);
        $this->assertEquals('item_type', $array['type']);
        $this->assertEquals('Product', $array['attributes']['name']);
        $this->assertEquals('product', $array['attributes']['api_key']);
        $this->assertFalse($array['attributes']['singleton']);
        $this->assertTrue($array['attributes']['sortable']);
        $this->assertFalse($array['attributes']['modular_block']);
        $this->assertFalse($array['attributes']['tree']);
        $this->assertTrue($array['attributes']['draft_mode_active']);
    }
}
