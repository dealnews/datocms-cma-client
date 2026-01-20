<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters;

use DealNews\DatoCMS\CMA\Parameters\Site;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Parameters\Site class
 */
class SiteTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues(): void {
        $params = new Site();

        $this->assertEquals([], $params->include);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyInclude(): void {
        $params = new Site();

        $array = $params->toArray();

        $this->assertArrayNotHasKey('include', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesIncludeWhenPopulated(): void {
        $params          = new Site();
        $params->include = ['item_types'];

        $array = $params->toArray();

        $this->assertArrayHasKey('include', $array);
        $this->assertEquals(['item_types'], $array['include']);
    }

    #[Group('unit')]
    public function testSettingIncludeWithSingleRelationship(): void {
        $params          = new Site();
        $params->include = ['item_types'];

        $this->assertEquals(['item_types'], $params->include);
    }

    #[Group('unit')]
    public function testSettingIncludeWithMultipleRelationships(): void {
        $params          = new Site();
        $params->include = ['item_types', 'account'];

        $this->assertEquals(['item_types', 'account'], $params->include);
    }

    #[Group('unit')]
    public function testSettingIncludeWithNestedRelationships(): void {
        $params          = new Site();
        $params->include = ['item_types.fields', 'item_types.singleton_item'];

        $this->assertEquals(['item_types.fields', 'item_types.singleton_item'], $params->include);
    }
}
