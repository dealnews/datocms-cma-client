<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use DealNews\DatoCMS\CMA\Input\Plugin;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Input\Plugin class
 */
#[Group('unit')]
class PluginTest extends TestCase {

    #[Group('unit')]
    public function testDefaultType(): void {
        $plugin = new Plugin();
        $this->assertEquals('plugin', $plugin->type);
    }

    #[Group('unit')]
    public function testDefaultIdIsNull(): void {
        $plugin = new Plugin();
        $this->assertNull($plugin->id);
    }

    #[Group('unit')]
    public function testDefaultAttributesIsEmptyArray(): void {
        $plugin = new Plugin();
        $this->assertEquals([], $plugin->attributes);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullId(): void {
        $plugin = new Plugin();
        $array  = $plugin->toArray();
        $this->assertArrayNotHasKey('id', $array);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyAttributes(): void {
        $plugin = new Plugin();
        $array  = $plugin->toArray();
        $this->assertArrayNotHasKey('attributes', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesIdWhenSet(): void {
        $plugin     = new Plugin();
        $plugin->id = 'plugin-123';
        $array      = $plugin->toArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertEquals('plugin-123', $array['id']);
    }

    #[Group('unit')]
    public function testToArrayIncludesAttributesWhenSet(): void {
        $plugin             = new Plugin();
        $plugin->attributes = ['package_name' => 'datocms-plugin-star-rating-editor'];
        $array              = $plugin->toArray();
        $this->assertArrayHasKey('attributes', $array);
        $this->assertEquals(['package_name' => 'datocms-plugin-star-rating-editor'], $array['attributes']);
    }

    #[Group('unit')]
    public function testToArrayAlwaysIncludesType(): void {
        $plugin = new Plugin();
        $array  = $plugin->toArray();
        $this->assertArrayHasKey('type', $array);
        $this->assertEquals('plugin', $array['type']);
    }
}
