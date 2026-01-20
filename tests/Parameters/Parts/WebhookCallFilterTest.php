<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters\Parts;

use DealNews\DatoCMS\CMA\Parameters\Parts\FilterFields;
use DealNews\DatoCMS\CMA\Parameters\Parts\WebhookCallFilter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Parameters\Parts\WebhookCallFilter class
 */
class WebhookCallFilterTest extends TestCase {

    // =========================================================================
    // Default values tests
    // =========================================================================

    #[Group('unit')]
    public function testDefaultValues() {
        $filter = new WebhookCallFilter();

        $this->assertNull($filter->ids);
        $this->assertInstanceOf(FilterFields::class, $filter->fields);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithEmptyFilter() {
        $filter = new WebhookCallFilter();

        $array = $filter->toArray();

        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    public function testToArrayWithIds() {
        $filter      = new WebhookCallFilter();
        $filter->ids = ['call-1', 'call-2', 'call-3'];

        $array = $filter->toArray();

        $this->assertEquals('call-1,call-2,call-3', $array['ids']);
    }

    #[Group('unit')]
    public function testToArrayWithFieldFilters() {
        $filter = new WebhookCallFilter();
        $filter->fields->addField('webhook_id', '123', 'eq');

        $array = $filter->toArray();

        $this->assertArrayHasKey('fields', $array);
        $this->assertEquals(['webhook_id' => ['eq' => '123']], $array['fields']);
    }

    #[Group('unit')]
    public function testToArrayWithMultipleFieldFilters() {
        $filter = new WebhookCallFilter();
        $filter->fields->addField('webhook_id', '123', 'eq');
        $filter->fields->addField('status', 'pending', 'eq');
        $filter->fields->addField('entity_type', 'item', 'eq');

        $array = $filter->toArray();

        $this->assertArrayHasKey('fields', $array);
        $this->assertEquals([
            'webhook_id'  => ['eq' => '123'],
            'status'      => ['eq' => 'pending'],
            'entity_type' => ['eq' => 'item'],
        ], $array['fields']);
    }

    #[Group('unit')]
    public function testToArrayWithDateFilters() {
        $filter = new WebhookCallFilter();
        $filter->fields->addField('last_sent_at', '2025-01-01', 'gt');
        $filter->fields->addField('next_retry_at', '2025-12-31', 'lt');
        $filter->fields->addField('created_at', '2025-01-15', 'gt');

        $array = $filter->toArray();

        $this->assertArrayHasKey('fields', $array);
        $this->assertEquals([
            'last_sent_at'  => ['gt' => '2025-01-01'],
            'next_retry_at' => ['lt' => '2025-12-31'],
            'created_at'    => ['gt' => '2025-01-15'],
        ], $array['fields']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyValues() {
        $filter = new WebhookCallFilter();
        $filter->fields->addField('webhook_id', '123', 'eq');

        $array = $filter->toArray();

        $this->assertArrayNotHasKey('ids', $array);
        $this->assertArrayHasKey('fields', $array);
    }

    #[Group('unit')]
    public function testToArrayWithIdsAndFieldFilters() {
        $filter      = new WebhookCallFilter();
        $filter->ids = ['call-1', 'call-2'];
        $filter->fields->addField('status', 'failed', 'eq');

        $array = $filter->toArray();

        $this->assertArrayHasKey('ids', $array);
        $this->assertEquals('call-1,call-2', $array['ids']);
        $this->assertArrayHasKey('fields', $array);
        $this->assertEquals(['status' => ['eq' => 'failed']], $array['fields']);
    }
}
