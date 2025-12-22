<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Parameters\Record as RecordParameter;
use DealNews\DatoCMS\CMA\Input\Record as RecordInput;

/**
 * API handler for DatoCMS record/item operations
 *
 * Provides methods for all record-related CRUD operations including listing,
 * creating, updating, deleting, publishing, and bulk operations.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $records = $client->record->list();
 * $record = $client->record->retrieve('record-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item
 */
class Record extends Base {

    /**
     * Return a list of records/items
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/instances
     *
     * @param RecordParameter|null $parameters Optional parameters for filtering,
     *                                         sorting, and pagination
     *
     * @return array<string, mixed> The API response body decoded as an
     *                              associative array
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(?RecordParameter $parameters = null): array {
        $query_params = !is_null($parameters) ? $parameters->toArray() : [];
        return $this->handler->execute('GET', '/items', $query_params);
    }

    /**
     * Create a new record/item
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/create
     *
     * @param array<string, mixed>|RecordInput $data Record data; method
     *                                               auto-wraps in {data: ...}
     *
     * @return array<string, mixed> The created record/item
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(array|RecordInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        return $this->handler->execute('POST', '/items', [], ['data' => $data]);
    }

    /**
     * Create a duplicate of a record/item
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/duplicate
     *
     * @param string $record_id The ID of the record/item to duplicate
     *
     * @return array<string, mixed> The duplicated record/item
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function duplicate(string $record_id): array {
        return $this->handler->execute('POST', '/items/' . $record_id . '/duplicate');
    }

    /**
     * Update an existing record/item
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/update
     *
     * @param string                       $record_id The ID of the record to update
     * @param array<string, mixed>|RecordInput $data  Updated record data; method
     *                                                auto-wraps in {data: ...}
     *
     * @return array<string, mixed> The updated record/item
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function update(string $record_id, array|RecordInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        return $this->handler->execute('POST', '/items/' . $record_id, ['data' => $data]);
    }

    /**
     * Retrieve records that reference the provided record/item
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/references
     *
     * @param string      $record_id The ID of the referenced record/item
     * @param bool        $nested    Include nested data structures (default: false)
     * @param string|null $version   Version filter: 'published', 'current', or null
     *                               for both (default: null)
     *
     * @return array<string, mixed> Records that reference the given ID
     *
     * @throws \InvalidArgumentException               If version is invalid
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function references(
        string $record_id,
        bool $nested = false,
        ?string $version = null
    ): array {
        $query_params = [];
        if ($nested) {
            $query_params['nested'] = true;
        }
        if (!empty($version)) {
            if (!in_array($version, ['published', 'current'])) {
                throw new \InvalidArgumentException('version must be "published" or "current"');
            }
            $query_params['version'] = $version;
        }
        return $this->handler->execute('GET', '/items/' . $record_id . '/references', $query_params);
    }

    /**
     * Retrieve a specific record/item by ID
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/self
     *
     * @param string $record_id The ID of the record/item
     * @param bool   $nested    Include nested data structures (default: false)
     * @param string $version   Version to retrieve: 'published' or 'current'
     *                          (default: 'current')
     *
     * @return array<string, mixed> The record/item data
     *
     * @throws \InvalidArgumentException               If version is invalid
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(
        string $record_id,
        bool $nested = false,
        string $version = 'current'
    ): array {
        if (!in_array($version, ['published', 'current'])) {
            throw new \InvalidArgumentException('version must be "published" or "current"');
        }
        $query_params = [
            'version' => $version,
        ];
        if ($nested) {
            $query_params['nested'] = true;
        }
        return $this->handler->execute('GET', '/items/' . $record_id, $query_params);
    }

    /**
     * Delete a specific record/item by ID
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/destroy
     *
     * @param string $record_id The ID of the record/item to delete
     *
     * @return array<string, mixed> Job info for the scheduled deletion
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $record_id): array {
        return $this->handler->execute('DELETE', '/items/' . $record_id);
    }

    /**
     * Publish a specific record/item by ID
     *
     * Supports selective publishing by specifying locales. When using selective
     * publishing, both $locales and $non_localized_content must be provided.
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/publish
     *
     * @param string     $record_id              The ID of the record to publish
     * @param bool       $recursive              Recursively publish connected items
     *                                           (default: false)
     * @param array<string>|null $locales        Limit publishing to these locales
     * @param bool|null  $non_localized_content  Publish non-localized content when
     *                                           using selective publishing
     *
     * @return array<string, mixed> The published record/item
     *
     * @throws \InvalidArgumentException               If selective publishing params
     *                                                 are incomplete
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function publish(
        string $record_id,
        bool $recursive = false,
        ?array $locales = null,
        ?bool $non_localized_content = null
    ): array {
        if (
            (is_null($non_localized_content) || is_null($locales)) &&
            (!is_null($non_localized_content) || !is_null($locales))
        ) {
            throw new \InvalidArgumentException('If you wish to use "Selective Publishing", both locales and non_localized_content must be set to a value besides NULL');
        }

        $put_data = [];
        if (!is_null($locales)) {
            $put_data = [
                'data' => [
                    'type' => 'selective_publish_operation',
                    'attributes' => [
                        'content_in_locales' => $locales,
                        'non_localized_content' => $non_localized_content,
                    ],
                ]
            ];
        }

        $query_params = [];
        if ($recursive) {
            $query_params['recursive'] = true;
        }

        return $this->handler->execute('PUT', '/items/' . $record_id . '/publish', $query_params, $put_data);
    }

    /**
     * Unpublish a specific record/item by ID
     *
     * Supports selective unpublishing by specifying locales.
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/unpublish
     *
     * @param string           $record_id The ID of the record to unpublish
     * @param bool             $recursive Recursively unpublish connected items
     *                                    (default: false)
     * @param array<string>|null $locales Limit unpublishing to these locales
     *
     * @return array<string, mixed> The unpublished record/item
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function unpublish(
        string $record_id,
        bool $recursive = false,
        ?array $locales = null
    ): array {
        $put_data = [];
        if (!is_null($locales)) {
            $put_data = [
                'data' => [
                    'type' => 'selective_unpublish_operation',
                    'attributes' => [
                        'content_in_locales' => $locales,
                    ],
                ]
            ];
        }

        $query_params = [];
        if ($recursive) {
            $query_params['recursive'] = true;
        }

        return $this->handler->execute('PUT', '/items/' . $record_id . '/unpublish', $query_params, $put_data);
    }

    /**
     * Publish multiple records/items at once
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/bulk_publish
     *
     * @param array<string> $record_ids List of record IDs to publish
     *
     * @return array<string, mixed> Job info for the scheduled bulk publish
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function publishBulk(array $record_ids): array {
        $post_data = [
            'data' => [
                'type' => 'item_bulk_publish_operation',
                'relationships' => [
                    'items' => [
                        'data' => [],
                    ]
                ],
            ]
        ];
        foreach ($record_ids as $record_id) {
            $post_data['data']['relationships']['items']['data'][] = [
                'type' => 'item',
                'id' => $record_id,
            ];
        }

        return $this->handler->execute('POST', '/items/bulk/publish', [], $post_data);
    }

    /**
     * Unpublish multiple records/items at once
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/bulk_unpublish
     *
     * @param array<string> $record_ids List of record IDs to unpublish
     *
     * @return array<string, mixed> Job info for the scheduled bulk unpublish
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function unpublishBulk(array $record_ids): array {
        $post_data = [
            'data' => [
                'type' => 'item_bulk_unpublish_operation',
                'relationships' => [
                    'items' => [
                        'data' => [],
                    ]
                ],
            ]
        ];
        foreach ($record_ids as $record_id) {
            $post_data['data']['relationships']['items']['data'][] = [
                'type' => 'item',
                'id' => $record_id,
            ];
        }

        return $this->handler->execute('POST', '/items/bulk/unpublish', [], $post_data);
    }

    /**
     * Delete multiple records/items at once
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/bulk_destroy
     *
     * @param array<string> $record_ids List of record IDs to delete
     *
     * @return array<string, mixed> Job info for the scheduled bulk deletion
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function deleteBulk(array $record_ids): array {
        $post_data = [
            'data' => [
                'type' => 'item_bulk_destroy_operation',
                'relationships' => [
                    'items' => [
                        'data' => [],
                    ]
                ],
            ]
        ];
        foreach ($record_ids as $record_id) {
            $post_data['data']['relationships']['items']['data'][] = [
                'type' => 'item',
                'id' => $record_id,
            ];
        }

        return $this->handler->execute('POST', '/items/bulk/destroy', [], $post_data);
    }

    /**
     * Move multiple records/items to a different workflow stage
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/bulk_move_to_stage
     *
     * @param array<string> $record_ids List of record IDs to move
     * @param string        $stage      Target stage name
     *
     * @return array<string, mixed> Job info for the scheduled stage move
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function moveToStageBulk(array $record_ids, string $stage): array {
        $post_data = [
            'data' => [
                'type' => 'item_bulk_move_to_stage_operation',
                'attributes' => [
                    'stage' => $stage,
                ],
                'relationships' => [
                    'items' => [
                        'data' => [],
                    ]
                ],
            ]
        ];
        foreach ($record_ids as $record_id) {
            $post_data['data']['relationships']['items']['data'][] = [
                'type' => 'item',
                'id' => $record_id,
            ];
        }

        return $this->handler->execute('POST', '/items/bulk/move-to-stage', [], $post_data);
    }
}