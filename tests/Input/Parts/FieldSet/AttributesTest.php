<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\FieldSet;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Parts\FieldSet\Attributes;

/**
 * Tests for the Input\Parts\FieldSet\Attributes class
 */
class AttributesTest extends TestCase {

    // =========================================================================
    // Default values tests
    // =========================================================================

    #[Group('unit')]
    public function testDefaultValues(): void {
        $attributes = new Attributes();

        $this->assertNull($attributes->title);
        $this->assertFalse($attributes->hint);
        $this->assertNull($attributes->position);
        $this->assertNull($attributes->collapsible);
        $this->assertNull($attributes->start_collapsed);
    }

    // =========================================================================
    // Property assignment tests
    // =========================================================================

    #[Group('unit')]
    public function testTitleCanBeSet(): void {
        $attributes = new Attributes();

        $attributes->title = 'Contact Information';

        $this->assertEquals('Contact Information', $attributes->title);
    }

    #[Group('unit')]
    public function testHintCanBeSetToString(): void {
        $attributes = new Attributes();

        $attributes->hint = 'Please fill in these fields!';

        $this->assertEquals('Please fill in these fields!', $attributes->hint);
    }

    #[Group('unit')]
    public function testHintCanBeSetToNull(): void {
        $attributes = new Attributes();

        $attributes->hint = null;

        $this->assertNull($attributes->hint);
    }

    #[Group('unit')]
    public function testPositionCanBeSet(): void {
        $attributes = new Attributes();

        $attributes->position = 10;

        $this->assertEquals(10, $attributes->position);
    }

    #[Group('unit')]
    public function testCollapsibleCanBeSet(): void {
        $attributes = new Attributes();

        $attributes->collapsible = true;

        $this->assertTrue($attributes->collapsible);
    }

    #[Group('unit')]
    public function testStartCollapsedCanBeSet(): void {
        $attributes = new Attributes();

        $attributes->start_collapsed = false;

        $this->assertFalse($attributes->start_collapsed);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithEmptyAttributesReturnsEmpty(): void {
        $attributes = new Attributes();

        $array = $attributes->toArray();

        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullTitle(): void {
        $attributes = new Attributes();
        $attributes->position = 5;

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('title', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesTitleWhenSet(): void {
        $attributes = new Attributes();
        $attributes->title = 'Contact Information';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('title', $array);
        $this->assertEquals('Contact Information', $array['title']);
    }

    #[Group('unit')]
    public function testToArrayExcludesFalseHint(): void {
        $attributes = new Attributes();
        $attributes->title = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('hint', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesHintWhenSetToString(): void {
        $attributes = new Attributes();
        $attributes->hint = 'Please fill in these fields!';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('hint', $array);
        $this->assertEquals('Please fill in these fields!', $array['hint']);
    }

    #[Group('unit')]
    public function testToArrayIncludesHintWhenSetToNull(): void {
        $attributes = new Attributes();
        $attributes->hint = null;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('hint', $array);
        $this->assertNull($array['hint']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullPosition(): void {
        $attributes = new Attributes();
        $attributes->title = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('position', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesPositionWhenSet(): void {
        $attributes = new Attributes();
        $attributes->position = 10;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('position', $array);
        $this->assertEquals(10, $array['position']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullCollapsible(): void {
        $attributes = new Attributes();
        $attributes->title = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('collapsible', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesCollapsibleWhenSet(): void {
        $attributes = new Attributes();
        $attributes->collapsible = true;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('collapsible', $array);
        $this->assertTrue($array['collapsible']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullStartCollapsed(): void {
        $attributes = new Attributes();
        $attributes->title = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('start_collapsed', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesStartCollapsedWhenSet(): void {
        $attributes = new Attributes();
        $attributes->start_collapsed = false;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('start_collapsed', $array);
        $this->assertFalse($array['start_collapsed']);
    }

    #[Group('unit')]
    public function testToArrayWithAllFieldsPopulated(): void {
        $attributes = new Attributes();
        $attributes->title = 'Contact Details';
        $attributes->hint = 'Enter contact information';
        $attributes->position = 10;
        $attributes->collapsible = true;
        $attributes->start_collapsed = false;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('title', $array);
        $this->assertEquals('Contact Details', $array['title']);
        $this->assertArrayHasKey('hint', $array);
        $this->assertEquals('Enter contact information', $array['hint']);
        $this->assertArrayHasKey('position', $array);
        $this->assertEquals(10, $array['position']);
        $this->assertArrayHasKey('collapsible', $array);
        $this->assertTrue($array['collapsible']);
        $this->assertArrayHasKey('start_collapsed', $array);
        $this->assertFalse($array['start_collapsed']);
    }
}
