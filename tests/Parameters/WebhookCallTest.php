<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Parameters\WebhookCall;
use DealNews\DatoCMS\CMA\Parameters\Parts\OrderBy;
use DealNews\DatoCMS\CMA\Parameters\Parts\Page;
use DealNews\DatoCMS\CMA\Parameters\Parts\WebhookCallFilter;

/**
 * Tests for the Parameters\WebhookCall class
 */
class WebhookCallTest extends TestCase {

    // =========================================================================
    // Constructor tests
    // =========================================================================

    #[Group('unit')]
    public function testConstructorInitializesFilter() {
        $params = new WebhookCall();

        $this->assertInstanceOf(WebhookCallFilter::class, $params->filter);
    }

    #[Group('unit')]
    public function testConstructorInitializesOrderBy() {
        $params = new WebhookCall();

        $this->assertInstanceOf(OrderBy::class, $params->order_by);
    }

    #[Group('unit')]
    public function testConstructorInitializesPage() {
        $params = new WebhookCall();

        $this->assertInstanceOf(Page::class, $params->page);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithEmptyParams() {
        $params = new WebhookCall();

        $array = $params->toArray();

        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    public function testToArrayWithFilter() {
        $params = new WebhookCall();
        $params->filter->ids = ['call-1', 'call-2'];

        $array = $params->toArray();

        $this->assertArrayHasKey('filter', $array);
        $this->assertEquals('call-1,call-2', $array['filter']['ids']);
    }

    #[Group('unit')]
    public function testToArrayWithOrderBy() {
        $params = new WebhookCall();
        $params->order_by->addOrderByField('created_at', 'DESC');

        $array = $params->toArray();

        $this->assertArrayHasKey('order_by', $array);
        $this->assertEquals('created_at_DESC', $array['order_by']);
    }

    #[Group('unit')]
    public function testToArrayWithPage() {
        $params = new WebhookCall();
        $params->page->limit = 25;
        $params->page->offset = 50;

        $array = $params->toArray();

        $this->assertArrayHasKey('page', $array);
        $this->assertEquals(25, $array['page']['limit']);
        $this->assertEquals(50, $array['page']['offset']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyFilter() {
        $params = new WebhookCall();
        $params->page->limit = 10;

        $array = $params->toArray();

        $this->assertArrayNotHasKey('filter', $array);
        $this->assertArrayHasKey('page', $array);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyOrderBy() {
        $params = new WebhookCall();
        $params->filter->ids = ['call-1'];

        $array = $params->toArray();

        $this->assertArrayNotHasKey('order_by', $array);
        $this->assertArrayHasKey('filter', $array);
    }
}
