<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Input\Webhook as WebhookInput;

/**
 * API handler for DatoCMS webhook operations
 *
 * Provides methods for all webhook-related CRUD operations including listing,
 * creating, updating, deleting, and retrieving webhooks.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $webhooks = $client->webhook->list();
 * $webhook = $client->webhook->retrieve('webhook-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/webhook
 */
class Webhook extends Base {

    /**
     * Create a new webhook
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook/create
     *
     * @param   array<string, mixed>|WebhookInput   $data   Webhook input data
     *
     * @return array<string, mixed>                         The created webhook
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(array|WebhookInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute('POST', '/webhooks', [], ['data' => $data]);
    }

    /**
     * Update a webhook
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook/update
     *
     * @param   string                              $webhook_id     ID of the webhook to update
     * @param   array<string, mixed>|WebhookInput   $data           Webhook input data
     *
     * @return array<string, mixed>                                 The updated webhook
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function update(string $webhook_id, array|WebhookInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute('PUT', '/webhooks/' . $webhook_id, [], ['data' => $data]);
    }

    /**
     * List all webhooks
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook/instances
     *
     * @return array<string, mixed>                    List of webhook instances
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(): array {
        return $this->handler->execute('GET', '/webhooks');
    }

    /**
     * Retrieve a webhook
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook/self
     *
     * @param   string                              $webhook_id     ID of the webhook to retrieve
     *
     * @return array<string, mixed>                                 The retrieved webhook
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $webhook_id): array {
        return $this->handler->execute('GET', '/webhooks/' . $webhook_id);
    }

    /**
     * Delete a webhook
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook/destroy
     *
     * @param   string                              $webhook_id     ID of the webhook to delete
     *
     * @return array<string, mixed>                                 The deleted webhook
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $webhook_id): array {
        return $this->handler->execute('DELETE', '/webhooks/' . $webhook_id);
    }
}
