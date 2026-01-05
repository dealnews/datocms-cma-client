<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\UploadCollection;

/**
 * Tests for the Input\UploadCollection class
 */
class UploadCollectionTest extends TestCase {

    // =========================================================================
    // Constructor tests
    // =========================================================================

    #[Group('unit')]
    public function testConstructorSetsDefaults() {
        $collection = new UploadCollection();

        $this->assertNull($collection->id);
        $this->assertEquals('upload_collection', $collection->type);
        $this->assertEquals([], $collection->attributes);
        $this->assertNull($collection->parent_id);
    }

    // =========================================================================
    // Type enforcement tests
    // =========================================================================

    #[Group('unit')]
    public function testTypeCanBeSetToUploadCollection() {
        $collection = new UploadCollection();

        $collection->type = 'upload_collection';

        $this->assertEquals('upload_collection', $collection->type);
    }

    #[Group('unit')]
    public function testTypeThrowsOnInvalidValue() {
        $collection = new UploadCollection();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type must be "upload_collection"');

        $collection->type = 'invalid';
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithMinimalData() {
        $collection = new UploadCollection();
        $collection->attributes['label'] = 'Product Images';

        $array = $collection->toArray();

        $this->assertEquals('upload_collection', $array['type']);
        $this->assertEquals(['label' => 'Product Images'], $array['attributes']);
        $this->assertArrayNotHasKey('id', $array);
        $this->assertArrayNotHasKey('relationships', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesIdWhenSet() {
        $collection = new UploadCollection();
        $collection->id = 'collection-123';
        $collection->attributes['label'] = 'My Collection';

        $array = $collection->toArray();

        $this->assertEquals('collection-123', $array['id']);
    }

    #[Group('unit')]
    public function testToArrayIncludesParentRelationship() {
        $collection = new UploadCollection();
        $collection->attributes['label'] = 'Subcollection';
        $collection->parent_id = 'parent-456';

        $array = $collection->toArray();

        $this->assertArrayHasKey('relationships', $array);
        $this->assertArrayHasKey('parent', $array['relationships']);
        $this->assertEquals([
            'data' => [
                'type' => 'upload_collection',
                'id'   => 'parent-456',
            ],
        ], $array['relationships']['parent']);
    }

    #[Group('unit')]
    public function testToArrayWithAllFields() {
        $collection = new UploadCollection();
        $collection->id = 'collection-123';
        $collection->attributes['label'] = 'Full Collection';
        $collection->parent_id = 'parent-456';

        $array = $collection->toArray();

        $this->assertEquals('collection-123', $array['id']);
        $this->assertEquals('upload_collection', $array['type']);
        $this->assertEquals(['label' => 'Full Collection'], $array['attributes']);
        $this->assertEquals([
            'parent' => [
                'data' => [
                    'type' => 'upload_collection',
                    'id'   => 'parent-456',
                ],
            ],
        ], $array['relationships']);
    }

    #[Group('unit')]
    public function testToArrayWithEmptyAttributes() {
        $collection = new UploadCollection();

        $array = $collection->toArray();

        $this->assertEquals('upload_collection', $array['type']);
        $this->assertEquals([], $array['attributes']);
    }
}
