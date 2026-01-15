<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Webhook;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Parts\Webhook\Attributes;
use DealNews\DatoCMS\CMA\Input\Parts\Webhook\Events;

/**
 * Tests for the Input\Parts\Webhook\Attributes class
 */
class AttributesTest extends TestCase {

    // =========================================================================
    // Default values tests
    // =========================================================================

    #[Group('unit')]
    public function testDefaultValues(): void {
        $attributes = new Attributes();

        $this->assertNull($attributes->name);
        $this->assertNull($attributes->url);
        $this->assertNull($attributes->headers);
        $this->assertNull($attributes->events);
        $this->assertFalse($attributes->custom_payload);
        $this->assertFalse($attributes->http_basic_user);
        $this->assertFalse($attributes->http_basic_password);
        $this->assertNull($attributes->enabled);
        $this->assertNull($attributes->payload_api_version);
        $this->assertNull($attributes->nested_items_in_payload);
        $this->assertNull($attributes->auto_retry);
    }

    // =========================================================================
    // Property assignment tests
    // =========================================================================

    #[Group('unit')]
    public function testNameCanBeSet(): void {
        $attributes = new Attributes();
        $attributes->name = 'My Webhook';

        $this->assertEquals('My Webhook', $attributes->name);
    }

    #[Group('unit')]
    public function testUrlCanBeSet(): void {
        $attributes = new Attributes();
        $attributes->url = 'https://example.com/webhook';

        $this->assertEquals('https://example.com/webhook', $attributes->url);
    }

    #[Group('unit')]
    public function testHeadersCanBeSetAsArray(): void {
        $attributes = new Attributes();
        $attributes->headers = ['Authorization' => 'Bearer token123', 'X-Custom' => 'value'];

        $this->assertEquals(['Authorization' => 'Bearer token123', 'X-Custom' => 'value'], $attributes->headers);
    }

    #[Group('unit')]
    public function testEventsCanBeSetAsArray(): void {
        $attributes = new Attributes();
        $attributes->events = [
            ['entity_type' => 'item', 'event_types' => ['create', 'update']],
        ];

        $this->assertEquals([['entity_type' => 'item', 'event_types' => ['create', 'update']]], $attributes->events);
    }

    #[Group('unit')]
    public function testEventsCanBeSetAsEventsObject(): void {
        $events = Events::init()->addEvent('item', ['create']);
        $attributes = new Attributes();
        $attributes->events = $events;

        $this->assertInstanceOf(Events::class, $attributes->events);
    }

    #[Group('unit')]
    public function testCustomPayloadCanBeSetToString(): void {
        $attributes = new Attributes();
        $attributes->custom_payload = '{"message": "test"}';

        $this->assertEquals('{"message": "test"}', $attributes->custom_payload);
    }

    #[Group('unit')]
    public function testCustomPayloadCanBeSetToNull(): void {
        $attributes = new Attributes();
        $attributes->custom_payload = null;

        $this->assertNull($attributes->custom_payload);
    }

    #[Group('unit')]
    public function testHttpBasicUserCanBeSetToString(): void {
        $attributes = new Attributes();
        $attributes->http_basic_user = 'username';

        $this->assertEquals('username', $attributes->http_basic_user);
    }

    #[Group('unit')]
    public function testHttpBasicUserCanBeSetToNull(): void {
        $attributes = new Attributes();
        $attributes->http_basic_user = null;

        $this->assertNull($attributes->http_basic_user);
    }

    #[Group('unit')]
    public function testHttpBasicPasswordCanBeSetToString(): void {
        $attributes = new Attributes();
        $attributes->http_basic_password = 'password123';

        $this->assertEquals('password123', $attributes->http_basic_password);
    }

    #[Group('unit')]
    public function testHttpBasicPasswordCanBeSetToNull(): void {
        $attributes = new Attributes();
        $attributes->http_basic_password = null;

        $this->assertNull($attributes->http_basic_password);
    }

    #[Group('unit')]
    public function testEnabledCanBeSet(): void {
        $attributes = new Attributes();
        $attributes->enabled = true;

        $this->assertTrue($attributes->enabled);
    }

    #[Group('unit')]
    public function testPayloadApiVersionCanBeSet(): void {
        $attributes = new Attributes();
        $attributes->payload_api_version = '3';

        $this->assertEquals('3', $attributes->payload_api_version);
    }

    #[Group('unit')]
    public function testNestedItemsInPayloadCanBeSet(): void {
        $attributes = new Attributes();
        $attributes->nested_items_in_payload = true;

        $this->assertTrue($attributes->nested_items_in_payload);
    }

    #[Group('unit')]
    public function testAutoRetryCanBeSet(): void {
        $attributes = new Attributes();
        $attributes->auto_retry = true;

        $this->assertTrue($attributes->auto_retry);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithEmptyAttributesReturnsEmpty(): void {
        $attributes = new Attributes();

        $array = $attributes->toArray();

        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullName(): void {
        $attributes = new Attributes();
        $attributes->url = 'https://example.com';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('name', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesNameWhenSet(): void {
        $attributes = new Attributes();
        $attributes->name = 'My Webhook';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertEquals('My Webhook', $array['name']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullUrl(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('url', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesUrlWhenSet(): void {
        $attributes = new Attributes();
        $attributes->url = 'https://example.com/webhook';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('url', $array);
        $this->assertEquals('https://example.com/webhook', $array['url']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullHeaders(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('headers', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesHeadersWhenSet(): void {
        $attributes = new Attributes();
        $attributes->headers = ['X-Custom' => 'value'];

        $array = $attributes->toArray();

        $this->assertArrayHasKey('headers', $array);
        $this->assertEquals(['X-Custom' => 'value'], $array['headers']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullEvents(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('events', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesEventsWhenSetAsArray(): void {
        $attributes = new Attributes();
        $attributes->events = [['entity_type' => 'item', 'event_types' => ['create']]];

        $array = $attributes->toArray();

        $this->assertArrayHasKey('events', $array);
        $this->assertEquals([['entity_type' => 'item', 'event_types' => ['create']]], $array['events']);
    }

    #[Group('unit')]
    public function testToArrayExcludesFalseCustomPayload(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('custom_payload', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesCustomPayloadWhenSetToString(): void {
        $attributes = new Attributes();
        $attributes->custom_payload = '{"message": "test"}';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('custom_payload', $array);
        $this->assertEquals('{"message": "test"}', $array['custom_payload']);
    }

    #[Group('unit')]
    public function testToArrayIncludesCustomPayloadWhenSetToNull(): void {
        $attributes = new Attributes();
        $attributes->custom_payload = null;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('custom_payload', $array);
        $this->assertNull($array['custom_payload']);
    }

    #[Group('unit')]
    public function testToArrayExcludesFalseHttpBasicUser(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('http_basic_user', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesHttpBasicUserWhenSetToString(): void {
        $attributes = new Attributes();
        $attributes->http_basic_user = 'username';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('http_basic_user', $array);
        $this->assertEquals('username', $array['http_basic_user']);
    }

    #[Group('unit')]
    public function testToArrayIncludesHttpBasicUserWhenSetToNull(): void {
        $attributes = new Attributes();
        $attributes->http_basic_user = null;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('http_basic_user', $array);
        $this->assertNull($array['http_basic_user']);
    }

    #[Group('unit')]
    public function testToArrayExcludesFalseHttpBasicPassword(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('http_basic_password', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesHttpBasicPasswordWhenSetToString(): void {
        $attributes = new Attributes();
        $attributes->http_basic_password = 'password123';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('http_basic_password', $array);
        $this->assertEquals('password123', $array['http_basic_password']);
    }

    #[Group('unit')]
    public function testToArrayIncludesHttpBasicPasswordWhenSetToNull(): void {
        $attributes = new Attributes();
        $attributes->http_basic_password = null;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('http_basic_password', $array);
        $this->assertNull($array['http_basic_password']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullEnabled(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('enabled', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesEnabledWhenSet(): void {
        $attributes = new Attributes();
        $attributes->enabled = true;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('enabled', $array);
        $this->assertTrue($array['enabled']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullPayloadApiVersion(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('payload_api_version', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesPayloadApiVersionWhenSet(): void {
        $attributes = new Attributes();
        $attributes->payload_api_version = '3';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('payload_api_version', $array);
        $this->assertEquals('3', $array['payload_api_version']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullNestedItemsInPayload(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('nested_items_in_payload', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesNestedItemsInPayloadWhenSet(): void {
        $attributes = new Attributes();
        $attributes->nested_items_in_payload = true;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('nested_items_in_payload', $array);
        $this->assertTrue($array['nested_items_in_payload']);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullAutoRetry(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('auto_retry', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesAutoRetryWhenSet(): void {
        $attributes = new Attributes();
        $attributes->auto_retry = true;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('auto_retry', $array);
        $this->assertTrue($array['auto_retry']);
    }

    #[Group('unit')]
    public function testToArrayWithAllFieldsPopulated(): void {
        $attributes = new Attributes();
        $attributes->name = 'Production Webhook';
        $attributes->url = 'https://api.example.com/webhook';
        $attributes->headers = ['Authorization' => 'Bearer token'];
        $attributes->events = [['entity_type' => 'item', 'event_types' => ['create', 'update']]];
        $attributes->custom_payload = '{"test": "payload"}';
        $attributes->http_basic_user = 'admin';
        $attributes->http_basic_password = 'secret';
        $attributes->enabled = true;
        $attributes->payload_api_version = '3';
        $attributes->nested_items_in_payload = false;
        $attributes->auto_retry = true;

        $array = $attributes->toArray();

        $this->assertEquals('Production Webhook', $array['name']);
        $this->assertEquals('https://api.example.com/webhook', $array['url']);
        $this->assertEquals(['Authorization' => 'Bearer token'], $array['headers']);
        $this->assertEquals([['entity_type' => 'item', 'event_types' => ['create', 'update']]], $array['events']);
        $this->assertEquals('{"test": "payload"}', $array['custom_payload']);
        $this->assertEquals('admin', $array['http_basic_user']);
        $this->assertEquals('secret', $array['http_basic_password']);
        $this->assertTrue($array['enabled']);
        $this->assertEquals('3', $array['payload_api_version']);
        $this->assertFalse($array['nested_items_in_payload']);
        $this->assertTrue($array['auto_retry']);
    }
}
