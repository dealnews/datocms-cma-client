<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Parameters\Model;
use DealNews\DatoCMS\CMA\Parameters\Parts\Page;

/**
 * Tests for the Model parameters class
 */
class ModelTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues(): void {
        $params = new Model();

        $this->assertInstanceOf(Page::class, $params->page);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyValues(): void {
        $params = new Model();

        $array = $params->toArray();

        $this->assertArrayNotHasKey('page', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesPageWhenNonDefault(): void {
        $params = new Model();
        $params->page->limit = 50;
        $params->page->offset = 100;

        $array = $params->toArray();

        $this->assertArrayHasKey('page', $array);
        $this->assertEquals(50, $array['page']['limit']);
        $this->assertEquals(100, $array['page']['offset']);
    }

    #[Group('unit')]
    public function testToArrayIncludesOnlyLimitWhenSet(): void {
        $params = new Model();
        $params->page->limit = 25;

        $array = $params->toArray();

        $this->assertArrayHasKey('page', $array);
        $this->assertEquals(25, $array['page']['limit']);
        $this->assertArrayNotHasKey('offset', $array['page']);
    }

    #[Group('unit')]
    public function testToArrayIncludesOnlyOffsetWhenSet(): void {
        $params = new Model();
        $params->page->offset = 50;

        $array = $params->toArray();

        $this->assertArrayHasKey('page', $array);
        $this->assertEquals(50, $array['page']['offset']);
        $this->assertArrayNotHasKey('limit', $array['page']);
    }
}
