<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\FieldSet;
use DealNews\DatoCMS\CMA\Input\Parts\FieldSet\Attributes;

/**
 * Tests for the Input\FieldSet class
 */
class FieldSetTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues(): void {
        $fieldset = new FieldSet();

        $this->assertNull($fieldset->id);
        $this->assertEquals('fieldset', $fieldset->type);
        $this->assertEquals([], $fieldset->attributes);
    }

    #[Group('unit')]
    public function testTypeCanBeSetToFieldSet(): void {
        $fieldset = new FieldSet();
        $fieldset->type = 'fieldset';

        $this->assertEquals('fieldset', $fieldset->type);
    }

    #[Group('unit')]
    public function testIdCanBeSet(): void {
        $fieldset = new FieldSet();
        $fieldset->id = 'fieldset-123';

        $this->assertEquals('fieldset-123', $fieldset->id);
    }

    #[Group('unit')]
    public function testAttributesCanBeSetWithArray(): void {
        $fieldset = new FieldSet();
        $fieldset->attributes['title'] = 'Contact Information';
        $fieldset->attributes['collapsible'] = true;

        $this->assertEquals('Contact Information', $fieldset->attributes['title']);
        $this->assertTrue($fieldset->attributes['collapsible']);
    }

    #[Group('unit')]
    public function testAttributesCanBeSetWithObject(): void {
        $attributes = new Attributes();
        $attributes->title = 'Contact Information';
        $attributes->collapsible = true;

        $fieldset = new FieldSet();
        $fieldset->attributes = $attributes;

        $this->assertInstanceOf(Attributes::class, $fieldset->attributes);
        $this->assertEquals('Contact Information', $fieldset->attributes->title);
        $this->assertTrue($fieldset->attributes->collapsible);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyId(): void {
        $fieldset = new FieldSet();
        $fieldset->attributes['title'] = 'Test';

        $array = $fieldset->toArray();

        $this->assertArrayNotHasKey('id', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesIdWhenSet(): void {
        $fieldset = new FieldSet();
        $fieldset->id = 'fieldset-123';
        $fieldset->attributes['title'] = 'Test';

        $array = $fieldset->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertEquals('fieldset-123', $array['id']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyAttributes(): void {
        $fieldset = new FieldSet();

        $array = $fieldset->toArray();

        $this->assertArrayNotHasKey('attributes', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesAttributesWhenSet(): void {
        $fieldset = new FieldSet();
        $fieldset->attributes['title'] = 'Contact Information';
        $fieldset->attributes['collapsible'] = true;

        $array = $fieldset->toArray();

        $this->assertArrayHasKey('attributes', $array);
        $this->assertEquals('Contact Information', $array['attributes']['title']);
        $this->assertTrue($array['attributes']['collapsible']);
    }

    #[Group('unit')]
    public function testFullObjectSerialization(): void {
        $attributes = new Attributes();
        $attributes->title = 'Contact Details';
        $attributes->hint = 'Enter contact information';
        $attributes->position = 10;
        $attributes->collapsible = true;
        $attributes->start_collapsed = false;

        $fieldset = new FieldSet();
        $fieldset->id = 'fieldset-456';
        $fieldset->attributes = $attributes;

        $array = $fieldset->toArray();

        $this->assertEquals('fieldset-456', $array['id']);
        $this->assertEquals('fieldset', $array['type']);
        $this->assertArrayHasKey('attributes', $array);
        $this->assertEquals('Contact Details', $array['attributes']['title']);
        $this->assertEquals('Enter contact information', $array['attributes']['hint']);
        $this->assertEquals(10, $array['attributes']['position']);
        $this->assertTrue($array['attributes']['collapsible']);
        $this->assertFalse($array['attributes']['start_collapsed']);
    }
}
