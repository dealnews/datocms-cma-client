<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters\Parts;

use DealNews\DatoCMS\CMA\Parameters\Parts\UploadFilter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Parameters\Parts\UploadFilter class
 */
class UploadFilterTest extends TestCase {

    // =========================================================================
    // Default values tests
    // =========================================================================

    #[Group('unit')]
    public function testDefaultValues() {
        $filter = new UploadFilter();

        $this->assertEquals([], $filter->ids);
        $this->assertNull($filter->type);
        $this->assertNull($filter->query);
        $this->assertNull($filter->upload_collection_id);
        $this->assertEquals([], $filter->smart_tags);
        $this->assertEquals([], $filter->tags);
        $this->assertNull($filter->author);
        $this->assertNull($filter->copyright);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithEmptyFilter() {
        $filter = new UploadFilter();

        $array = $filter->toArray();

        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    public function testToArrayWithIds() {
        $filter      = new UploadFilter();
        $filter->ids = ['id1', 'id2', 'id3'];

        $array = $filter->toArray();

        $this->assertEquals('id1,id2,id3', $array['ids']);
    }

    #[Group('unit')]
    public function testToArrayWithType() {
        $filter       = new UploadFilter();
        $filter->type = 'image';

        $array = $filter->toArray();

        $this->assertEquals('image', $array['type']);
    }

    #[Group('unit')]
    public function testToArrayWithQuery() {
        $filter        = new UploadFilter();
        $filter->query = 'banner hero';

        $array = $filter->toArray();

        $this->assertEquals('banner hero', $array['query']);
    }

    #[Group('unit')]
    public function testToArrayWithUploadCollectionId() {
        $filter                       = new UploadFilter();
        $filter->upload_collection_id = 'collection-123';

        $array = $filter->toArray();

        $this->assertEquals('collection-123', $array['upload_collection_id']);
    }

    #[Group('unit')]
    public function testToArrayWithSmartTags() {
        $filter             = new UploadFilter();
        $filter->smart_tags = ['person', 'outdoor'];

        $array = $filter->toArray();

        $this->assertEquals('person,outdoor', $array['smart_tags']);
    }

    #[Group('unit')]
    public function testToArrayWithTags() {
        $filter       = new UploadFilter();
        $filter->tags = ['banner', 'featured'];

        $array = $filter->toArray();

        $this->assertEquals('banner,featured', $array['tags']);
    }

    #[Group('unit')]
    public function testToArrayWithAuthor() {
        $filter         = new UploadFilter();
        $filter->author = 'John Doe';

        $array = $filter->toArray();

        $this->assertEquals('John Doe', $array['author']);
    }

    #[Group('unit')]
    public function testToArrayWithCopyright() {
        $filter            = new UploadFilter();
        $filter->copyright = '© 2025';

        $array = $filter->toArray();

        $this->assertEquals('© 2025', $array['copyright']);
    }

    #[Group('unit')]
    public function testToArrayWithMultipleFilters() {
        $filter                       = new UploadFilter();
        $filter->type                 = 'image';
        $filter->query                = 'banner';
        $filter->tags                 = ['hero', 'featured'];
        $filter->upload_collection_id = 'collection-123';

        $array = $filter->toArray();

        $this->assertEquals('image', $array['type']);
        $this->assertEquals('banner', $array['query']);
        $this->assertEquals('hero,featured', $array['tags']);
        $this->assertEquals('collection-123', $array['upload_collection_id']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyArrays() {
        $filter       = new UploadFilter();
        $filter->type = 'video';

        $array = $filter->toArray();

        $this->assertArrayNotHasKey('ids', $array);
        $this->assertArrayNotHasKey('smart_tags', $array);
        $this->assertArrayNotHasKey('tags', $array);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullValues() {
        $filter      = new UploadFilter();
        $filter->ids = ['id1'];

        $array = $filter->toArray();

        $this->assertArrayNotHasKey('type', $array);
        $this->assertArrayNotHasKey('query', $array);
        $this->assertArrayNotHasKey('upload_collection_id', $array);
        $this->assertArrayNotHasKey('author', $array);
        $this->assertArrayNotHasKey('copyright', $array);
    }
}
