<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Input\ScheduledPublication as ScheduledPublicationInput;

/**
 * API handler for DatoCMS record/item scheduled publication operations
 *
 * Provides methods for scheduling publication of records/items
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $scheduled_publication = $client->scheduled_publication->create('record-id', ['attributes' => ['publication_scheduled_at' => '2030-09-01T12:00:00Z']]);
 * $record = $client->scheduled_publication->delete('record-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/scheduled-publication
 */
class ScheduledPublication extends Base {

    /**
     * Create a new schedule for publishing a record/item
     *
     * @see /docs/content-management-api/resources/scheduled-publication/create
     *
     * @param string                                            $record_id  The id of the record/item we want to schedule for publishing
     * @param array<string, mixed>|ScheduledPublicationInput    $data       Record data; method auto-wraps in {data: ...}
     *
     * @return array<string, mixed>                                         The created scheduled publication information
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     * @throws \InvalidArgumentException               If publication_scheduled_at is not set
     */
    public function create(string $record_id, array|ScheduledPublicationInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        if (empty($data['attributes']['publication_scheduled_at'])) {
            throw new \InvalidArgumentException('publication_scheduled_at must be set to an ISO 8601 date/time in the \'attributes\'');
        }
        return $this->handler->execute('POST', '/items/' . $record_id . '/scheduled-publication', [], ['data' => $data]);
    }

    /**
     * Delete the scheduled publication for a record/item
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/scheduled-publication/destroy
     *
     * @param   string                  $record_id      The ID of the record/item that we want to delete the scheduled publication for
     *
     * @return  array<string, mixed>                    The record/item that we deleted the scheduled publication for
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $record_id): array {
        return $this->handler->execute('DELETE', '/items/' . $record_id . '/scheduled-publication');
    }
}