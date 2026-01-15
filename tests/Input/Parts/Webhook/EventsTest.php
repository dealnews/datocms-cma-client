<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Webhook;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Parts\Webhook\Events;
use DealNews\DatoCMS\CMA\Input\Parts\Webhook\EventFilters;

/**
 * Tests for the Input\Parts\Webhook\Events class
 */
class EventsTest extends TestCase {

    // =========================================================================
    // init() tests
    // =========================================================================

    #[Group('unit')]
    public function testInitReturnsInstance(): void {
        $events = Events::init();

        $this->assertInstanceOf(Events::class, $events);
    }

    // =========================================================================
    // addEvent() tests
    // =========================================================================

    #[Group('unit')]
    public function testAddEventWithValidEntityType(): void {
        $events = Events::init();
        $result = $events->addEvent('item', ['create', 'update']);

        $this->assertInstanceOf(Events::class, $result);
    }

    #[Group('unit')]
    public function testAddEventWithInvalidEntityType(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event type must be one of');

        $events = Events::init();
        $events->addEvent('invalid_type', ['create']);
    }

    #[Group('unit')]
    public function testAddEventWithFiltersAsArray(): void {
        $events = Events::init();
        $filters = [
            ['filter' => ['type' => 'environment', 'type_id' => ['primary']]],
        ];
        $result = $events->addEvent('item', ['create'], $filters);

        $this->assertInstanceOf(Events::class, $result);
    }

    #[Group('unit')]
    public function testAddEventWithFiltersAsEventFiltersObject(): void {
        $filters = EventFilters::init()->addFilter('environment', ['primary']);
        $events = Events::init();
        $result = $events->addEvent('item', ['create'], $filters);

        $this->assertInstanceOf(Events::class, $result);
    }

    #[Group('unit')]
    public function testAddEventMethodChaining(): void {
        $events = Events::init()
            ->addEvent('item', ['create'])
            ->addEvent('upload', ['update'])
            ->addEvent('item_type', ['delete']);

        $this->assertInstanceOf(Events::class, $events);
    }

    #[Group('unit')]
    public function testAllValidEntityTypesWork(): void {
        $events = Events::init();

        $events->addEvent('item_type', ['create']);
        $events->addEvent('item', ['create']);
        $events->addEvent('upload', ['create']);
        $events->addEvent('build_trigger', ['create']);
        $events->addEvent('environment', ['create']);
        $events->addEvent('maintenance_mode', ['create']);
        $events->addEvent('sso_user', ['create']);
        $events->addEvent('cda_cache_tags', ['create']);

        $this->assertInstanceOf(Events::class, $events);
    }

    // =========================================================================
    // jsonSerialize() tests
    // =========================================================================

    #[Group('unit')]
    public function testJsonSerializeReturnsCorrectStructure(): void {
        $events = Events::init()
            ->addEvent('item', ['create', 'update'])
            ->addEvent('upload', ['delete']);

        $result = $events->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('item', $result[0]['event_type']);
        $this->assertEquals(['create', 'update'], $result[0]['event_types']);
        $this->assertEquals('upload', $result[1]['event_type']);
        $this->assertEquals(['delete'], $result[1]['event_types']);
    }

    #[Group('unit')]
    public function testJsonSerializeWithFilters(): void {
        $filters = EventFilters::init()->addFilter('environment', ['primary']);
        $events = Events::init()
            ->addEvent('item', ['create'], $filters);

        $result = $events->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('filters', $result[0]);
        $this->assertIsArray($result[0]['filters']);
    }

    #[Group('unit')]
    public function testJsonSerializeReturnsEmptyArrayForNoEvents(): void {
        $events = Events::init();

        $result = $events->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
