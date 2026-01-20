<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\WebhookCall;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Parameters\WebhookCall as WebhookCallParameters;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\WebhookCall class
 */
class WebhookCallTest extends TestCase {

    /**
     * Creates a WebhookCall API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return WebhookCall
     */
    protected function createWebhookCallWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): WebhookCall {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new WebhookCall($mock_handler);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testListWithoutParameters(): void {
        $expected_response = ['data' => [['id' => 'call-1'], ['id' => 'call-2']]];
        $webhook_call      = $this->createWebhookCallWithMock('GET', '/webhook_calls', [], [], $expected_response);

        $result = $webhook_call->list();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testListWithParameters(): void {
        $parameters              = new WebhookCallParameters();
        $parameters->filter->ids = ['call-1', 'call-2'];
        $parameters->order_by->addOrderByField('created_at', 'DESC');
        $parameters->page->limit  = 10;
        $parameters->page->offset = 5;

        $expected_query = [
            'filter' => [
                'ids' => 'call-1,call-2',
            ],
            'order_by' => 'created_at_DESC',
            'page'     => [
                'limit'  => 10,
                'offset' => 5,
            ],
        ];

        $expected_response = ['data' => [['id' => 'call-1']]];
        $webhook_call      = $this->createWebhookCallWithMock('GET', '/webhook_calls', $expected_query, [], $expected_response);

        $result = $webhook_call->list($parameters);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve(): void {
        $expected_response = ['data' => ['id' => 'call-123', 'type' => 'webhook_call']];
        $webhook_call      = $this->createWebhookCallWithMock(
            'GET',
            '/webhook_calls/call-123',
            [],
            [],
            $expected_response
        );

        $result = $webhook_call->retrieve('call-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // resend() tests
    // =========================================================================

    #[Group('unit')]
    public function testResend(): void {
        $expected_response = ['data' => ['id' => 'call-123', 'type' => 'webhook_call']];
        $webhook_call      = $this->createWebhookCallWithMock(
            'POST',
            '/webhook_calls/call-123/resend_webhook',
            [],
            [],
            $expected_response
        );

        $result = $webhook_call->resend('call-123');

        $this->assertEquals($expected_response, $result);
    }
}
