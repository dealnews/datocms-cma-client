<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Record;
use DealNews\DatoCMS\CMA\Input\Parts\Meta;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships;
use DealNews\DatoCMS\CMA\DataTypes\Scalar;
use DealNews\DatoCMS\CMA\DataTypes\Color;
use DealNews\DatoCMS\CMA\DataTypes\Location;
use DealNews\DatoCMS\CMA\DataTypes\Asset;
use DealNews\DatoCMS\CMA\DataTypes\SEO;

class RecordTest extends TestCase {

    #[Group('unit')]
    public function testDefaultTypeIsItem() {
        $record = new Record();
        
        $this->assertEquals('item', $record->type);
    }

    #[Group('unit')]
    public function testDefaultIdIsNull() {
        $record = new Record();
        
        $this->assertNull($record->id);
    }

    #[Group('unit')]
    public function testConstructorCreatesMetaObject() {
        $record = new Record();
        
        $this->assertInstanceOf(Meta::class, $record->meta);
    }

    #[Group('unit')]
    public function testConstructorCreatesRelationshipsObject() {
        $record = new Record();
        
        $this->assertInstanceOf(Relationships::class, $record->relationships);
    }

    #[Group('unit')]
    public function testConstructorWithItemTypeIdSetsRelationship() {
        $record = new Record('model_123');
        
        $this->assertEquals('model_123', $record->relationships->item_type->id);
    }

    #[Group('unit')]
    public function testConstructorWithoutItemTypeIdLeavesIdEmpty() {
        $record = new Record();
        
        $this->assertEquals('', $record->relationships->item_type->id);
    }

    #[Group('unit')]
    public function testCannotChangeTypeFromItem() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type must be "item"');
        
        $record = new Record();
        $record->type = 'not_item';
    }

    #[Group('unit')]
    public function testSettingId() {
        $record = new Record();
        $record->id = 'record_456';
        
        $this->assertEquals('record_456', $record->id);
    }

    #[Group('unit')]
    public function testSettingAttributesWithScalarValues() {
        $record = new Record();
        $record->attributes['title'] = 'My Title';
        $record->attributes['count'] = 42;
        $record->attributes['active'] = true;
        
        $this->assertEquals('My Title', $record->attributes['title']);
        $this->assertEquals(42, $record->attributes['count']);
        $this->assertTrue($record->attributes['active']);
    }

    #[Group('unit')]
    public function testSettingAttributesWithDataTypesObjects() {
        $record = new Record();
        
        $scalar = Scalar::init();
        $scalar->set('text value');
        $record->attributes['description'] = $scalar;
        
        $this->assertInstanceOf(Scalar::class, $record->attributes['description']);
    }

    #[Group('unit')]
    public function testSettingMetaProperties() {
        $record = new Record();
        $record->meta->created_at = '2025-12-19T10:00:00Z';
        $record->meta->stage = 'published';
        
        $this->assertEquals('2025-12-19T10:00:00Z', $record->meta->created_at);
        $this->assertEquals('published', $record->meta->stage);
    }

    #[Group('unit')]
    public function testSettingRelationships() {
        $record = new Record();
        $record->relationships->item_type->id = 'model_789';
        $record->relationships->creator->type = 'user';
        $record->relationships->creator->id = 'user_123';
        
        $this->assertEquals('model_789', $record->relationships->item_type->id);
        $this->assertEquals('user', $record->relationships->creator->type);
        $this->assertEquals('user_123', $record->relationships->creator->id);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyId() {
        $record = new Record('model_123');
        
        $array = $record->toArray();
        
        $this->assertArrayNotHasKey('id', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesIdWhenSet() {
        $record = new Record('model_123');
        $record->id = 'record_456';
        
        $array = $record->toArray();
        
        $this->assertArrayHasKey('id', $array);
        $this->assertEquals('record_456', $array['id']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyMeta() {
        $record = new Record('model_123');
        
        $array = $record->toArray();
        
        $this->assertArrayNotHasKey('meta', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesMetaWhenSet() {
        $record = new Record('model_123');
        $record->meta->created_at = '2025-12-19T10:00:00Z';
        
        $array = $record->toArray();
        
        $this->assertArrayHasKey('meta', $array);
        $this->assertEquals(['created_at' => '2025-12-19T10:00:00Z'], $array['meta']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyAttributes() {
        $record = new Record('model_123');
        
        $array = $record->toArray();
        
        $this->assertArrayNotHasKey('attributes', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesScalarAttributes() {
        $record = new Record('model_123');
        $record->attributes['title'] = 'My Title';
        $record->attributes['count'] = 42;
        
        $array = $record->toArray();
        
        $this->assertArrayHasKey('attributes', $array);
        $this->assertEquals('My Title', $array['attributes']['title']);
        $this->assertEquals(42, $array['attributes']['count']);
    }

    #[Group('unit')]
    public function testToArraySerializesDataTypesObjectsViaExport() {
        $record = new Record('model_123');
        
        $scalar = Scalar::init();
        $scalar->set('text value');
        $record->attributes['description'] = $scalar;
        
        $array = $record->toArray();
        
        $this->assertEquals('text value', $array['attributes']['description']);
    }

    #[Group('unit')]
    public function testToArraySerializesJsonSerializableObjects() {
        $record = new Record('model_123');
        
        // Create an anonymous class that implements JsonSerializable
        $jsonObj = new class implements \JsonSerializable {
            public function jsonSerialize(): mixed {
                return ['custom' => 'data'];
            }
        };
        
        $record->attributes['custom_field'] = $jsonObj;
        
        $array = $record->toArray();
        
        $this->assertEquals(['custom' => 'data'], $array['attributes']['custom_field']);
    }

    #[Group('unit')]
    public function testToArrayThrowsExceptionForInvalidObject() {
        $this->expectException(\LogicException::class);
        // Note: Error message comes from the vendor ValueObject class which
        // processes the object before our toArray() method does.
        $this->expectExceptionMessage('Propety invalid_field does not implement the Export or JsonSerializable interface');
        
        $record = new Record('model_123');
        
        // Create an object that implements neither Export nor JsonSerializable
        $invalidObj = new \stdClass();
        $record->attributes['invalid_field'] = $invalidObj;
        
        $record->toArray();
    }

    #[Group('unit')]
    public function testFullRecordWithAllFieldsPopulated() {
        $record = new Record('model_123');
        $record->id = 'record_456';
        $record->attributes['title'] = 'Full Record';
        $record->attributes['count'] = 100;
        $record->meta->created_at = '2025-12-19T10:00:00Z';
        $record->meta->stage = 'published';
        $record->relationships->creator->type = 'user';
        $record->relationships->creator->id = 'user_789';
        
        $array = $record->toArray();
        
        $this->assertEquals([
            'id' => 'record_456',
            'type' => 'item',
            'attributes' => [
                'title' => 'Full Record',
                'count' => 100,
            ],
            'meta' => [
                'created_at' => '2025-12-19T10:00:00Z',
                'stage' => 'published',
            ],
            'relationships' => [
                'item_type' => [
                    'data' => [
                        'type' => 'item_type',
                        'id' => 'model_123',
                    ]
                ],
                'creator' => [
                    'data' => [
                        'type' => 'user',
                        'id' => 'user_789',
                    ]
                ],
            ],
        ], $array);
    }

    #[Group('unit')]
    public function testRecordWithScalarDataType() {
        $record = new Record('model_123');
        
        $scalar = Scalar::init();
        $scalar->set('scalar text');
        $record->attributes['description'] = $scalar;
        
        $array = $record->toArray();
        
        $this->assertEquals('scalar text', $array['attributes']['description']);
    }

    #[Group('unit')]
    public function testRecordWithColorDataType() {
        $record = new Record('model_123');
        
        $color = Color::init();
        $color->setColor(255, 128, 64, 200);
        $record->attributes['brand_color'] = $color;
        
        $array = $record->toArray();
        
        $this->assertEquals([
            'red' => 255,
            'green' => 128,
            'blue' => 64,
            'alpha' => 200,
        ], $array['attributes']['brand_color']);
    }

    #[Group('unit')]
    public function testRecordWithLocationDataType() {
        $record = new Record('model_123');
        
        $location = Location::init();
        $location->setLocation(40.7128, -74.0060);
        $record->attributes['coordinates'] = $location;
        
        $array = $record->toArray();
        
        $this->assertEquals([
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ], $array['attributes']['coordinates']);
    }

    #[Group('unit')]
    public function testRecordWithAssetDataType() {
        $record = new Record('model_123');
        
        $asset = Asset::init();
        $asset->setAsset('upload_123', 'Asset Title', 'Alt Text');
        $record->attributes['image'] = $asset;
        
        $array = $record->toArray();
        
        $this->assertEquals([
            'upload_id' => 'upload_123',
            'title' => 'Asset Title',
            'alt' => 'Alt Text',
        ], $array['attributes']['image']);
    }

    #[Group('unit')]
    public function testRecordWithSEODataType() {
        $record = new Record('model_123');
        
        $seo = SEO::init();
        $seo->setSEO('Page Title', 'Page Description', 'image_id', 'summary', false);
        $record->attributes['seo'] = $seo;
        
        $array = $record->toArray();
        
        $this->assertEquals([
            'title' => 'Page Title',
            'description' => 'Page Description',
            'image' => 'image_id',
            'twitter_card' => 'summary',
            'no_index' => false,
        ], $array['attributes']['seo']);
    }

    #[Group('unit')]
    public function testRecordWithLocalizedDataTypes() {
        $record = new Record('model_123');
        
        $scalar = Scalar::init();
        $scalar->addLocale('en', 'English text');
        $scalar->addLocale('es', 'Spanish text');
        $scalar->addLocale('fr', 'French text');
        $record->attributes['title'] = $scalar;
        
        $array = $record->toArray();
        
        $this->assertEquals([
            'en' => 'English text',
            'es' => 'Spanish text',
            'fr' => 'French text',
        ], $array['attributes']['title']);
    }

    #[Group('unit')]
    public function testRecordWithMixedAttributeTypes() {
        $record = new Record('model_123');
        
        // Scalar values
        $record->attributes['title'] = 'Mixed Types';
        $record->attributes['count'] = 50;
        
        // DataType object
        $color = Color::init();
        $color->setColor(128, 128, 128, 255);
        $record->attributes['color'] = $color;
        
        // Localized DataType object
        $description = Scalar::init();
        $description->addLocale('en', 'English description');
        $description->addLocale('es', 'Spanish description');
        $record->attributes['description'] = $description;
        
        $array = $record->toArray();
        
        $this->assertEquals('Mixed Types', $array['attributes']['title']);
        $this->assertEquals(50, $array['attributes']['count']);
        $this->assertEquals([
            'red' => 128,
            'green' => 128,
            'blue' => 128,
            'alpha' => 255,
        ], $array['attributes']['color']);
        $this->assertEquals([
            'en' => 'English description',
            'es' => 'Spanish description',
        ], $array['attributes']['description']);
    }
}
