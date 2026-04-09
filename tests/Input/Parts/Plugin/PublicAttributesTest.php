<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Plugin;

use DealNews\DatoCMS\CMA\Input\Parts\Plugin\PublicAttributes;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Input\Parts\Plugin\PublicAttributes
 */
#[Group('unit')]
class PublicAttributesTest extends TestCase {

    #[Group('unit')]
    public function testDefaultPackageNameIsEmptyString(): void {
        $attrs = new PublicAttributes();
        $this->assertEquals('', $attrs->package_name);
    }

    #[Group('unit')]
    public function testToArrayIncludesPackageName(): void {
        $attrs               = new PublicAttributes();
        $attrs->package_name = 'datocms-plugin-star-rating-editor';
        $array               = $attrs->toArray();
        $this->assertArrayHasKey('package_name', $array);
        $this->assertEquals('datocms-plugin-star-rating-editor', $array['package_name']);
    }

    #[Group('unit')]
    public function testToArrayOnlyHasPackageName(): void {
        $attrs               = new PublicAttributes();
        $attrs->package_name = 'datocms-plugin-star-rating-editor';
        $array               = $attrs->toArray();
        $this->assertCount(1, $array);
    }
}
