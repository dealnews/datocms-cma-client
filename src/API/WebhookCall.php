<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Parameters\WebhookCall as WebhookCallParameters;

/**
 * API handler for DatoCMS field operations
 *
 * Provides methods for all webhook-call-related operations including listing,
 * retrieving, and re-sending webhook calls.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $calls = $client->webhook_call->list();
 * $call = $client->webhook_call->retrieve('webhook-call-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/webhook-call
 */
class WebhookCall extends Base {

    /**
     * List all webhooks calls
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook-call/instances
     *
     * @param   WebhookCallParameters|null  $parameters     Optional parameters for filtering,
     *                                                      sorting, and pagination
     *
     * @return array<string, mixed>                         List of webhook calls
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(?WebhookCallParameters $parameters = null): array {
        $query_params = !is_null($parameters) ? $parameters->toArray() : [];
        return $this->handler->execute('GET', '/webhook_calls', $query_params);
    }

    /**
     * Retrieve a webhook call
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook-call/self
     *
     * @param   string  $webhook_call_id    ID of the webhook call to retrieve
     *
     * @return array<string, mixed>         Webhook call details
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $webhook_call_id): array {
        return $this->handler->execute('GET', '/webhook_calls/' . $webhook_call_id);
    }

    /**
     * Re-send the webhook call
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook-call/resend_webhook
     *
     * @param   string  $webhook_call_id    ID of the webhook call to re-send
     *
     * @return array<string, mixed>         Webhook call details
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function resend(string $webhook_call_id): array {
        return $this->handler->execute('POST', '/webhook_calls/' . $webhook_call_id . '/resend_webhook');
    }
}
