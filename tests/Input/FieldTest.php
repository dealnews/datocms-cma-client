<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Field;
use DealNews\DatoCMS\CMA\Input\Parts\Field\Attributes;
use DealNews\DatoCMS\CMA\Input\Parts\Field\Relationships;

/**
 * Tests for the Input\Field class
 */
class FieldTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues(): void {
        $field = new Field();

        $this->assertNull($field->id);
        $this->assertEquals('field', $field->type);
        $this->assertEquals([], $field->attributes);
        $this->assertNull($field->relationships);
    }

    #[Group('unit')]
    public function testTypeCanBeSetToField(): void {
        $field = new Field();
        $field->type = 'field';

        $this->assertEquals('field', $field->type);
    }

    #[Group('unit')]
    public function testAttributesCanBeSetAsArray(): void {
        $field = new Field();
        $field->attributes['label'] = 'Title';
        $field->attributes['field_type'] = 'string';
        $field->attributes['api_key'] = 'title';

        $this->assertEquals('Title', $field->attributes['label']);
        $this->assertEquals('string', $field->attributes['field_type']);
        $this->assertEquals('title', $field->attributes['api_key']);
    }

    #[Group('unit')]
    public function testAttributesCanBeSetAsObject(): void {
        $attributes = new Attributes();
        $attributes->label = 'Title';
        $attributes->field_type = 'string';
        $attributes->api_key = 'title';

        $field = new Field();
        $field->attributes = $attributes;

        $this->assertInstanceOf(Attributes::class, $field->attributes);
        $this->assertEquals('Title', $field->attributes->label);
        $this->assertEquals('string', $field->attributes->field_type);
        $this->assertEquals('title', $field->attributes->api_key);
    }

    #[Group('unit')]
    public function testRelationshipsCanBeSet(): void {
        $relationships = new Relationships();

        $field = new Field();
        $field->relationships = $relationships;

        $this->assertInstanceOf(Relationships::class, $field->relationships);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullId(): void {
        $field = new Field();
        $field->attributes['label'] = 'Test';

        $array = $field->toArray();

        $this->assertArrayNotHasKey('id', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesIdWhenSet(): void {
        $field = new Field();
        $field->id = 'field-123';
        $field->attributes['label'] = 'Test';

        $array = $field->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertEquals('field-123', $array['id']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyAttributes(): void {
        $field = new Field();

        $array = $field->toArray();

        $this->assertArrayNotHasKey('attributes', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesAttributesWhenSet(): void {
        $field = new Field();
        $field->attributes['label'] = 'Title';
        $field->attributes['field_type'] = 'string';

        $array = $field->toArray();

        $this->assertArrayHasKey('attributes', $array);
        $this->assertEquals('Title', $array['attributes']['label']);
        $this->assertEquals('string', $array['attributes']['field_type']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullRelationships(): void {
        $field = new Field();
        $field->attributes['label'] = 'Test';

        $array = $field->toArray();

        $this->assertArrayNotHasKey('relationships', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesRelationshipsWhenSet(): void {
        $relationships = new Relationships();

        $field = new Field();
        $field->relationships = $relationships;
        $field->attributes['label'] = 'Test';

        $array = $field->toArray();

        $this->assertArrayHasKey('relationships', $array);
    }

    #[Group('unit')]
    public function testFullFieldSerialization(): void {
        $attributes = new Attributes();
        $attributes->label = 'Product Title';
        $attributes->field_type = 'string';
        $attributes->api_key = 'product_title';
        $attributes->localized = true;
        $attributes->hint = 'Enter the product title';

        $relationships = new Relationships();

        $field = new Field();
        $field->id = 'field-789';
        $field->attributes = $attributes;
        $field->relationships = $relationships;

        $array = $field->toArray();

        $this->assertEquals('field-789', $array['id']);
        $this->assertEquals('field', $array['type']);
        $this->assertArrayHasKey('attributes', $array);
        $this->assertArrayHasKey('relationships', $array);
    }
}
