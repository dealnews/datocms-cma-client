<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters\Parts;

use DealNews\DatoCMS\CMA\Parameters\Parts\FilterFields;
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

    #[Group('unit')]
    public function testDefaultValuesIncludesFields() {
        $filter = new UploadFilter();

        $this->assertInstanceOf(FilterFields::class, $filter->fields);
    }

    #[Group('unit')]
    public function testFieldsIsFilterFieldsInstance() {
        $filter = new UploadFilter();

        $this->assertInstanceOf(FilterFields::class, $filter->fields);
    }

    // =========================================================================
    // toArray() tests - existing functionality
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

    // =========================================================================
    // toArray() tests - fields functionality
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithFieldsFilter() {
        $filter = new UploadFilter();
        $filter->fields->addField('width', 1000, 'gte');

        $array = $filter->toArray();

        $this->assertArrayHasKey('fields', $array);
        $this->assertEquals(['gte' => 1000], $array['fields']['width']);
    }

    #[Group('unit')]
    public function testToArrayWithMultipleFieldFilters() {
        $filter = new UploadFilter();
        $filter->fields->addField('width', 1000, 'gte');
        $filter->fields->addField('author', 'John', 'matches');
        $filter->fields->addField('created_at', '2025-01-01', 'gt');

        $array = $filter->toArray();

        $this->assertArrayHasKey('fields', $array);
        $this->assertEquals(['gte' => 1000], $array['fields']['width']);
        $this->assertEquals(['matches' => 'John'], $array['fields']['author']);
        $this->assertEquals(['gt' => '2025-01-01'], $array['fields']['created_at']);
    }

    #[Group('unit')]
    public function testToArrayWithFieldsAndDirectProperties() {
        $filter       = new UploadFilter();
        $filter->type = 'image';
        $filter->fields->addField('width', 1000, 'gte');

        $array = $filter->toArray();

        $this->assertEquals('image', $array['type']);
        $this->assertArrayHasKey('fields', $array);
        $this->assertEquals(['gte' => 1000], $array['fields']['width']);
    }

    #[Group('unit')]
    public function testToArrayWithEmptyFieldsExcludesFields() {
        $filter       = new UploadFilter();
        $filter->type = 'image';
        // Don't add any field filters

        $array = $filter->toArray();

        $this->assertArrayNotHasKey('fields', $array);
    }

    #[Group('unit')]
    public function testToArrayWithComplexFieldOperators() {
        $filter = new UploadFilter();
        $filter->fields->addField('size', 1000000, 'lt');
        $filter->fields->addField('height', 500, 'lte');
        $filter->fields->addField('mime_type', 'image/jpeg', 'eq');
        $filter->fields->addField('is_image', true, 'eq');

        $array = $filter->toArray();

        $this->assertEquals(['lt' => 1000000], $array['fields']['size']);
        $this->assertEquals(['lte' => 500], $array['fields']['height']);
        $this->assertEquals(['eq' => 'image/jpeg'], $array['fields']['mime_type']);
        $this->assertEquals(['eq' => true], $array['fields']['is_image']);
    }

    #[Group('unit')]
    public function testFieldsChaining() {
        $filter = new UploadFilter();
        
        $result = $filter->fields->addField('width', 1000, 'gte')
                                  ->addField('height', 500, 'gte');

        $this->assertSame($filter->fields, $result);

        $array = $filter->toArray();
        $this->assertEquals(['gte' => 1000], $array['fields']['width']);
        $this->assertEquals(['gte' => 500], $array['fields']['height']);
    }

    #[Group('unit')]
    public function testToArrayWithMultipleOperatorsOnSameField() {
        $filter = new UploadFilter();
        $filter->fields->addField('width', 500, 'gte');
        $filter->fields->addField('width', 2000, 'lte');

        $array = $filter->toArray();

        $this->assertEquals(
            ['gte' => 500, 'lte' => 2000],
            $array['fields']['width']
        );
    }
}
