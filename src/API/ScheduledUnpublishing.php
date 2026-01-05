<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Input\ScheduledUnpublishing as ScheduledUnpublishingInput;

/**
 * API handler for DatoCMS record/item scheduled unpublication operations
 *
 * Provides methods for scheduling unpublication of records/items
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $scheduled_unpublication = $client->scheduled_unpublishing->create('record-id', ['type' => 'scheduled_unpublishing', 'attributes' => ['unpublishing_scheduled_at' => '2030-09-01T12:00:00Z']]);
 * $record = $client->scheduled_unpublishing->delete('record-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/scheduled-unpublishing
 */
class ScheduledUnpublishing extends Base {

    /**
     * Create a new schedule for unpublishing a record/item
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/scheduled-unpublishing/create
     *
     * @param string                                            $record_id  The id of the record/item we want to schedule for unpublishing
     * @param array<string, mixed>|ScheduledUnpublishingInput   $data       Record data; method auto-wraps in {data: ...}
     *
     * @return array<string, mixed>                                         The created scheduled unpublication information
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     * @throws \InvalidArgumentException               If unpublishing_scheduled_at is not set
     */
    public function create(string $record_id, array|ScheduledUnpublishingInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        if (empty($data['attributes']['unpublishing_scheduled_at'])) {
            throw new \InvalidArgumentException('unpublishing_scheduled_at must be set to an ISO 8601 date/time in the \'attributes\'');
        }
        return $this->handler->execute('POST', '/items/' . $record_id . '/scheduled-unpublishing', [], ['data' => $data]);
    }

    /**
     * Delete the scheduled unpublication for a record/item
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/scheduled-unpublishing/destroy
     *
     * @param   string                  $record_id      The ID of the record/item that we want to delete the scheduled unpublication for
     *
     * @return  array<string, mixed>                    The record/item that we deleted the scheduled unpublication for
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $record_id): array {
        return $this->handler->execute('DELETE', '/items/' . $record_id . '/scheduled-unpublishing');
    }
}