<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Webhook;
use DealNews\DatoCMS\CMA\Input\Parts\Webhook\Attributes;

/**
 * Tests for the Input\Webhook class
 */
class WebhookTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues(): void {
        $webhook = new Webhook();

        $this->assertNull($webhook->id);
        $this->assertEquals('webhook', $webhook->type);
        $this->assertEquals([], $webhook->attributes);
    }

    #[Group('unit')]
    public function testTypeCanBeSetToWebhook(): void {
        $webhook = new Webhook();
        $webhook->type = 'webhook';

        $this->assertEquals('webhook', $webhook->type);
    }

    #[Group('unit')]
    public function testIdCanBeSet(): void {
        $webhook = new Webhook();
        $webhook->id = 'webhook-123';

        $this->assertEquals('webhook-123', $webhook->id);
    }

    #[Group('unit')]
    public function testAttributesCanBeSetWithArray(): void {
        $webhook = new Webhook();
        $webhook->attributes['name'] = 'Test Webhook';
        $webhook->attributes['url'] = 'https://example.com/webhook';

        $this->assertEquals('Test Webhook', $webhook->attributes['name']);
        $this->assertEquals('https://example.com/webhook', $webhook->attributes['url']);
    }

    #[Group('unit')]
    public function testAttributesCanBeSetWithObject(): void {
        $attributes = new Attributes();
        $attributes->name = 'Test Webhook';
        $attributes->url = 'https://example.com/webhook';

        $webhook = new Webhook();
        $webhook->attributes = $attributes;

        $this->assertInstanceOf(Attributes::class, $webhook->attributes);
        $this->assertEquals('Test Webhook', $webhook->attributes->name);
        $this->assertEquals('https://example.com/webhook', $webhook->attributes->url);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyId(): void {
        $webhook = new Webhook();
        $webhook->attributes['name'] = 'Test';

        $array = $webhook->toArray();

        $this->assertArrayNotHasKey('id', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesIdWhenSet(): void {
        $webhook = new Webhook();
        $webhook->id = 'webhook-123';
        $webhook->attributes['name'] = 'Test';

        $array = $webhook->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertEquals('webhook-123', $array['id']);
    }
}
