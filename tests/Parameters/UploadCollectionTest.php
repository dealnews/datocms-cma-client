<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Parameters\UploadCollection;
use DealNews\DatoCMS\CMA\Parameters\Parts\Page;

/**
 * Tests for the Parameters\UploadCollection class
 */
class UploadCollectionTest extends TestCase {

    // =========================================================================
    // Constructor tests
    // =========================================================================

    #[Group('unit')]
    public function testConstructorInitializesPage() {
        $params = new UploadCollection();

        $this->assertInstanceOf(Page::class, $params->page);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithEmptyParams() {
        $params = new UploadCollection();

        $array = $params->toArray();

        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    public function testToArrayWithPage() {
        $params = new UploadCollection();
        $params->page->limit = 25;
        $params->page->offset = 50;

        $array = $params->toArray();

        $this->assertArrayHasKey('page', $array);
        $this->assertEquals(25, $array['page']['limit']);
        $this->assertEquals(50, $array['page']['offset']);
    }

    #[Group('unit')]
    public function testToArrayWithOnlyLimit() {
        $params = new UploadCollection();
        $params->page->limit = 100;

        $array = $params->toArray();

        $this->assertArrayHasKey('page', $array);
        $this->assertEquals(100, $array['page']['limit']);
    }

    #[Group('unit')]
    public function testExtendsCommon() {
        $params = new UploadCollection();

        $this->assertInstanceOf(\DealNews\DatoCMS\CMA\Parameters\Common::class, $params);
    }
}
