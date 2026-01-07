<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Site;
use DealNews\DatoCMS\CMA\Input\Parts\Site\Attributes;
use DealNews\DatoCMS\CMA\Input\Parts\Site\Meta;
use DealNews\DatoCMS\CMA\Input\Parts\Site\Relationships;

/**
 * Tests for the Input\Site class
 */
class SiteTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues(): void {
        $site = new Site();

        $this->assertEquals('site', $site->type);
        $this->assertEquals([], $site->attributes);
        $this->assertEquals([], $site->meta);
        $this->assertNull($site->relationships);
    }

    #[Group('unit')]
    public function testTypeCannotBeChanged(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type must be "site"');

        $site = new Site();
        $site->type = 'invalid';
    }

    #[Group('unit')]
    public function testTypeCanBeSetToSite(): void {
        $site = new Site();
        $site->type = 'site';

        $this->assertEquals('site', $site->type);
    }

    #[Group('unit')]
    public function testAttributesCanBeSetWithArray(): void {
        $site = new Site();
        $site->attributes = ['no_index' => true];

        $this->assertEquals(['no_index' => true], $site->attributes);
    }

    #[Group('unit')]
    public function testAttributesCanBeSetWithObject(): void {
        $site = new Site();
        $attributes = new Attributes();
        $attributes->no_index = true;
        $site->attributes = $attributes;

        $this->assertInstanceOf(Attributes::class, $site->attributes);
        $this->assertTrue($site->attributes->no_index);
    }

    #[Group('unit')]
    public function testMetaCanBeSetWithArray(): void {
        $site = new Site();
        $site->meta = ['test' => 'value'];

        $this->assertEquals(['test' => 'value'], $site->meta);
    }

    #[Group('unit')]
    public function testMetaCanBeSetWithObject(): void {
        $site = new Site();
        $meta = new Meta();
        $site->meta = $meta;

        $this->assertInstanceOf(Meta::class, $site->meta);
    }

    #[Group('unit')]
    public function testRelationshipsCanBeSet(): void {
        $site = new Site();
        $relationships = new Relationships();
        $site->relationships = $relationships;

        $this->assertInstanceOf(Relationships::class, $site->relationships);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyAttributes(): void {
        $site = new Site();

        $array = $site->toArray();

        $this->assertArrayNotHasKey('attributes', $array);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyMeta(): void {
        $site = new Site();

        $array = $site->toArray();

        $this->assertArrayNotHasKey('meta', $array);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullRelationships(): void {
        $site = new Site();

        $array = $site->toArray();

        $this->assertArrayNotHasKey('relationships', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesPopulatedValues(): void {
        $site = new Site();
        $site->attributes = ['no_index' => true];
        $site->meta = ['test' => 'value'];
        $relationships = new Relationships();
        $site->relationships = $relationships;

        $array = $site->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertEquals('site', $array['type']);
        $this->assertArrayHasKey('attributes', $array);
        $this->assertEquals(['no_index' => true], $array['attributes']);
        $this->assertArrayHasKey('meta', $array);
        $this->assertEquals(['test' => 'value'], $array['meta']);
        $this->assertArrayHasKey('relationships', $array);
    }
}
