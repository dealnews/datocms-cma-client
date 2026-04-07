<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Plugin;

use DealNews\DatoCMS\CMA\Input\Parts\Plugin\PrivateAttributes;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Input\Parts\Plugin\PrivateAttributes
 */
#[Group('unit')]
class PrivateAttributesTest extends TestCase {

    #[Group('unit')]
    public function testDefaultNameIsEmptyString(): void {
        $attrs = new PrivateAttributes();
        $this->assertEquals('', $attrs->name);
    }

    #[Group('unit')]
    public function testDefaultDescriptionIsFalse(): void {
        $attrs = new PrivateAttributes();
        $this->assertFalse($attrs->description);
    }

    #[Group('unit')]
    public function testDefaultUrlIsEmptyString(): void {
        $attrs = new PrivateAttributes();
        $this->assertEquals('', $attrs->url);
    }

    #[Group('unit')]
    public function testDefaultPermissionsIsNull(): void {
        $attrs = new PrivateAttributes();
        $this->assertNull($attrs->permissions);
    }

    #[Group('unit')]
    public function testToArrayExcludesDescriptionWhenFalse(): void {
        $attrs = new PrivateAttributes();
        $array = $attrs->toArray();
        $this->assertArrayNotHasKey('description', $array);
    }

    #[Group('unit')]
    public function testToArrayExcludesPermissionsWhenNull(): void {
        $attrs = new PrivateAttributes();
        $array = $attrs->toArray();
        $this->assertArrayNotHasKey('permissions', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesDescriptionWhenNull(): void {
        $attrs              = new PrivateAttributes();
        $attrs->description = null;
        $array              = $attrs->toArray();
        $this->assertArrayHasKey('description', $array);
        $this->assertNull($array['description']);
    }

    #[Group('unit')]
    public function testToArrayIncludesDescriptionWhenString(): void {
        $attrs              = new PrivateAttributes();
        $attrs->description = 'A custom plugin';
        $array              = $attrs->toArray();
        $this->assertArrayHasKey('description', $array);
        $this->assertEquals('A custom plugin', $array['description']);
    }

    #[Group('unit')]
    public function testToArrayIncludesPermissionsWhenArray(): void {
        $attrs              = new PrivateAttributes();
        $attrs->permissions = ['read_items', 'write_items'];
        $array              = $attrs->toArray();
        $this->assertArrayHasKey('permissions', $array);
        $this->assertEquals(['read_items', 'write_items'], $array['permissions']);
    }

    #[Group('unit')]
    public function testToArrayFullyPopulated(): void {
        $attrs              = new PrivateAttributes();
        $attrs->name        = 'My Plugin';
        $attrs->description = 'Does something useful';
        $attrs->url         = 'https://example.com/plugin.js';
        $attrs->permissions = ['read_items'];

        $array = $attrs->toArray();

        $this->assertEquals('My Plugin', $array['name']);
        $this->assertEquals('Does something useful', $array['description']);
        $this->assertEquals('https://example.com/plugin.js', $array['url']);
        $this->assertEquals(['read_items'], $array['permissions']);
    }
}
