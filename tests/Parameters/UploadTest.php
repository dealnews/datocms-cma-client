<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters;

use DealNews\DatoCMS\CMA\Parameters\Parts\OrderBy;
use DealNews\DatoCMS\CMA\Parameters\Parts\Page;
use DealNews\DatoCMS\CMA\Parameters\Parts\UploadFilter;
use DealNews\DatoCMS\CMA\Parameters\Upload;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Parameters\Upload class
 */
class UploadTest extends TestCase {

    // =========================================================================
    // Constructor tests
    // =========================================================================

    #[Group('unit')]
    public function testConstructorInitializesOrderBy() {
        $params = new Upload();

        $this->assertInstanceOf(OrderBy::class, $params->order_by);
    }

    #[Group('unit')]
    public function testConstructorInitializesFilter() {
        $params = new Upload();

        $this->assertInstanceOf(UploadFilter::class, $params->filter);
    }

    #[Group('unit')]
    public function testConstructorInitializesPage() {
        $params = new Upload();

        $this->assertInstanceOf(Page::class, $params->page);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithEmptyParams() {
        $params = new Upload();

        $array = $params->toArray();

        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    public function testToArrayWithFilter() {
        $params                = new Upload();
        $params->filter->type  = 'image';
        $params->filter->query = 'banner';

        $array = $params->toArray();

        $this->assertArrayHasKey('filter', $array);
        $this->assertEquals('image', $array['filter']['type']);
        $this->assertEquals('banner', $array['filter']['query']);
    }

    #[Group('unit')]
    public function testToArrayWithOrderBy() {
        $params = new Upload();
        $params->order_by->addOrderByField('created_at', 'DESC');

        $array = $params->toArray();

        $this->assertArrayHasKey('order_by', $array);
        $this->assertEquals('created_at_DESC', $array['order_by']);
    }

    #[Group('unit')]
    public function testToArrayWithMultipleOrderBy() {
        $params = new Upload();
        $params->order_by->addOrderByField('created_at', 'DESC');
        $params->order_by->addOrderByField('filename', 'ASC');

        $array = $params->toArray();

        $this->assertEquals('created_at_DESC,filename_ASC', $array['order_by']);
    }

    #[Group('unit')]
    public function testToArrayWithPage() {
        $params               = new Upload();
        $params->page->limit  = 25;
        $params->page->offset = 50;

        $array = $params->toArray();

        $this->assertArrayHasKey('page', $array);
        $this->assertEquals(25, $array['page']['limit']);
        $this->assertEquals(50, $array['page']['offset']);
    }

    #[Group('unit')]
    public function testToArrayWithAllParams() {
        $params               = new Upload();
        $params->filter->type = 'image';
        $params->filter->tags = ['banner'];
        $params->order_by->addOrderByField('created_at', 'DESC');
        $params->page->limit = 10;

        $array = $params->toArray();

        $this->assertArrayHasKey('filter', $array);
        $this->assertArrayHasKey('order_by', $array);
        $this->assertArrayHasKey('page', $array);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyFilter() {
        $params              = new Upload();
        $params->page->limit = 10;

        $array = $params->toArray();

        $this->assertArrayNotHasKey('filter', $array);
        $this->assertArrayHasKey('page', $array);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyOrderBy() {
        $params               = new Upload();
        $params->filter->type = 'video';

        $array = $params->toArray();

        $this->assertArrayNotHasKey('order_by', $array);
        $this->assertArrayHasKey('filter', $array);
    }
}
