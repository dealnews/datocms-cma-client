<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\API\Webhook;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\Webhook as WebhookInput;

/**
 * Tests for the API\Webhook class
 */
#[Group('unit')]
class WebhookTest extends TestCase {

    /**
     * Creates a Webhook API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return Webhook
     */
    protected function createWebhookWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): Webhook {
        $mock = $this->createMock(Handler::class);
        $mock->expects($this->once())
             ->method('execute')
             ->with($expected_method, $expected_path, $expected_query, $expected_data)
             ->willReturn($return_value);

        return new Webhook($mock);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArray(): void {
        $data = [
            'type' => 'webhook',
            'attributes' => [
                'name' => 'Test Webhook',
                'url' => 'https://example.com/webhook',
            ],
        ];
        $expected_response = ['data' => ['id' => 'webhook-123', 'type' => 'webhook']];
        $webhook = $this->createWebhookWithMock(
            'POST',
            '/webhooks',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $webhook->create($data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithWebhookInput(): void {
        $input = new WebhookInput();
        $input->attributes['name'] = 'Test Webhook';
        $input->attributes['url'] = 'https://example.com/webhook';

        $expected_data = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'webhook-456', 'type' => 'webhook']];
        $webhook = $this->createWebhookWithMock(
            'POST',
            '/webhooks',
            [],
            $expected_data,
            $expected_response
        );

        $result = $webhook->create($input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // update() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateWithArray(): void {
        $data = [
            'type' => 'webhook',
            'attributes' => [
                'name' => 'Updated Webhook',
            ],
        ];
        $expected_response = ['data' => ['id' => 'webhook-123', 'type' => 'webhook']];
        $webhook = $this->createWebhookWithMock(
            'PUT',
            '/webhooks/webhook-123',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $webhook->update('webhook-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUpdateWithWebhookInput(): void {
        $input = new WebhookInput();
        $input->id = 'webhook-123';
        $input->attributes['name'] = 'Updated Webhook';

        $expected_data = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'webhook-123', 'type' => 'webhook']];
        $webhook = $this->createWebhookWithMock(
            'PUT',
            '/webhooks/webhook-123',
            [],
            $expected_data,
            $expected_response
        );

        $result = $webhook->update('webhook-123', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testList(): void {
        $expected_response = ['data' => [['id' => 'webhook-1'], ['id' => 'webhook-2']]];
        $webhook = $this->createWebhookWithMock(
            'GET',
            '/webhooks',
            [],
            [],
            $expected_response
        );

        $result = $webhook->list();

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve(): void {
        $expected_response = ['data' => ['id' => 'webhook-123', 'type' => 'webhook']];
        $webhook = $this->createWebhookWithMock(
            'GET',
            '/webhooks/webhook-123',
            [],
            [],
            $expected_response
        );

        $result = $webhook->retrieve('webhook-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete(): void {
        $expected_response = ['data' => ['id' => 'webhook-123', 'type' => 'webhook']];
        $webhook = $this->createWebhookWithMock(
            'DELETE',
            '/webhooks/webhook-123',
            [],
            [],
            $expected_response
        );

        $result = $webhook->delete('webhook-123');

        $this->assertEquals($expected_response, $result);
    }
}
