<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Webhook;

use DealNews\DatoCMS\CMA\Input\Parts\Webhook\EventFilters;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Input\Parts\Webhook\EventFilters class
 */
class EventFiltersTest extends TestCase {

    // =========================================================================
    // init() tests
    // =========================================================================

    #[Group('unit')]
    public function testInitReturnsInstance(): void {
        $filters = EventFilters::init();

        $this->assertInstanceOf(EventFilters::class, $filters);
    }

    // =========================================================================
    // addFilter() tests
    // =========================================================================

    #[Group('unit')]
    public function testAddFilterWithValidEntityType(): void {
        $filters = EventFilters::init();
        $result  = $filters->addFilter('item', ['item-123']);

        $this->assertInstanceOf(EventFilters::class, $result);
    }

    #[Group('unit')]
    public function testAddFilterWithInvalidEntityType(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event type must be one of');

        $filters = EventFilters::init();
        $filters->addFilter('invalid_type', ['id-123']);
    }

    #[Group('unit')]
    public function testAddFilterMethodChaining(): void {
        $filters = EventFilters::init()
            ->addFilter('item', ['item-123'])
            ->addFilter('environment', ['main'])
            ->addFilter('item_type', ['model-456']);

        $this->assertInstanceOf(EventFilters::class, $filters);
    }

    #[Group('unit')]
    public function testAllValidEntityTypesWork(): void {
        $filters = EventFilters::init();

        $filters->addFilter('item_type', ['model-1']);
        $filters->addFilter('item', ['item-1']);
        $filters->addFilter('build_trigger', ['trigger-1']);
        $filters->addFilter('environment', ['main']);
        $filters->addFilter('environment_type', ['primary']);

        $this->assertInstanceOf(EventFilters::class, $filters);
    }

    // =========================================================================
    // jsonSerialize() tests
    // =========================================================================

    #[Group('unit')]
    public function testJsonSerializeReturnsCorrectStructure(): void {
        $filters = EventFilters::init()
            ->addFilter('item', ['item-123', 'item-456'])
            ->addFilter('environment', ['main']);

        $result = $filters->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('item', $result[0]['entity_type']);
        $this->assertEquals(['item-123', 'item-456'], $result[0]['entity_ids']);
        $this->assertEquals('environment', $result[1]['entity_type']);
        $this->assertEquals(['main'], $result[1]['entity_ids']);
    }

    #[Group('unit')]
    public function testJsonSerializeReturnsEmptyArrayForNoFilters(): void {
        $filters = EventFilters::init();

        $result = $filters->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
