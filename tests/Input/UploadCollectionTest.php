<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use DealNews\DatoCMS\CMA\Input\UploadCollection;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

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
        $this->assertInstanceOf(\DealNews\DatoCMS\CMA\Input\Parts\UploadCollection\Relationships::class, $collection->relationships);
    }

    #[Group('unit')]
    public function testConstructorInitializesRelationships() {
        $collection = new UploadCollection();

        $this->assertInstanceOf(\DealNews\DatoCMS\CMA\Input\Parts\UploadCollection\Relationships::class, $collection->relationships);
        $this->assertInstanceOf(\DealNews\DatoCMS\CMA\Input\Parts\Relationships\UploadCollection::class, $collection->relationships->parent);
        $this->assertEquals([], $collection->relationships->children);
    }

    // =========================================================================
    // Type enforcement tests
    // =========================================================================

    #[Group('unit')]
    public function testTypeCanBeSetToUploadCollection() {
        $collection = new UploadCollection();

        $this->assertEquals('upload_collection', $collection->type);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithMinimalData() {
        $collection                      = new UploadCollection();
        $collection->attributes['label'] = 'Product Images';

        $array = $collection->toArray();

        $this->assertEquals('upload_collection', $array['type']);
        $this->assertEquals(['label' => 'Product Images'], $array['attributes']);
        $this->assertArrayNotHasKey('id', $array);
        $this->assertArrayNotHasKey('relationships', $array);
    }

    #[Group('unit')]
    public function testToArrayTypeProperlySet() {
        $collection     = new UploadCollection();
        $collection->id = 'collection-123';

        $array = $collection->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertEquals('upload_collection', $array['type']);
    }

    #[Group('unit')]
    public function testToArrayIncludesIdWhenSet() {
        $collection                      = new UploadCollection();
        $collection->id                  = 'collection-123';
        $collection->attributes['label'] = 'My Collection';

        $array = $collection->toArray();

        $this->assertEquals('collection-123', $array['id']);
    }

    #[Group('unit')]
    public function testToArrayIncludesParentRelationship() {
        $collection                            = new UploadCollection();
        $collection->attributes['label']       = 'Subcollection';
        $collection->relationships->parent->id = 'parent-456';

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
        $collection                            = new UploadCollection();
        $collection->id                        = 'collection-123';
        $collection->attributes['label']       = 'Full Collection';
        $collection->relationships->parent->id = 'parent-456';

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

    #[Group('unit')]
    public function testToArrayIncludesChildrenRelationships() {
        $collection                      = new UploadCollection();
        $collection->attributes['label'] = 'Parent Collection';

        $child1                                = new \DealNews\DatoCMS\CMA\Input\Parts\Relationships\UploadCollection();
        $child1->id                            = 'child-1';
        $collection->relationships->children[] = $child1;

        $child2                                = new \DealNews\DatoCMS\CMA\Input\Parts\Relationships\UploadCollection();
        $child2->id                            = 'child-2';
        $collection->relationships->children[] = $child2;

        $array = $collection->toArray();

        $this->assertArrayHasKey('relationships', $array);
        $this->assertArrayHasKey('children', $array['relationships']);
        $this->assertEquals([
            'data' => [
                ['type' => 'upload_collection', 'id' => 'child-1'],
                ['type' => 'upload_collection', 'id' => 'child-2'],
            ],
        ], $array['relationships']['children']);
    }
}
