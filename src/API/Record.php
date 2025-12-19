<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Parameters\Record as RecordParameter;
use DealNews\DatoCMS\CMA\Input\Record as RecordInput;

class Record extends Base {

    /**
     * Return a list of records/items
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/instances?language=http
     *
     * @param   null|RecordParameter    $parameters     Optional parameters to pass in order to filter, sort, paginate, etc...
     *
     * @return  array                                   The API response body decoded into an associative array
     */
    public function list(?RecordParameter $parameters = null) : array {
        $query_params = !is_null($parameters) ? $parameters->toArray() : [];
        return $this->handler->execute('GET', '/items', $query_params);
    }

    /**
     * Create a new record/item
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/create?language=http
     *
     * @param   array|RecordInput   $data       The record/item data to create the item (method will auto-wrap in {data: <your-data>})
     *
     * @return  array                           If successful, will return the record/item
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
     * @see https://www.datocms.com/docs/content-management-api/resources/item/duplicate?language=http
     *
     * @param   string  $record_id      The id of the record/item you wish to duplicate
     *
     * @return  array                   If successful, will return the duplicated record/item
     */
    public function duplicate(string $record_id): array {
        return $this->handler->execute('POST', '/items/' . $record_id . '/duplicate');
    }

    /**
     * Update an existing record/item
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/update?language=http
     *
     * @param   string              $record_id      The id of the record/item you wish to update
     * @param   array|RecordInput   $data           The record/item data to update the item (method will auto-wrap in {data: <your-data>})
     *
     * @return  array                               If successful, will return the updated record/item
     */
    public function update(string $record_id, array|RecordInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        return $this->handler->execute('POST', '/items/' . $record_id, ['data' => $data]);
    }

    /**
     * Retrieve records that reference the provided record/item id
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/references?language=http
     *
     * @param   string          $record_id      The id of the record/item that is referenced
     * @param   bool            $nested         Should the returned records contain nested data structures?
     * @param   string|null     $version        Should we return the 'published' or 'current' versions of the items? Defaults to both.
     *
     * @return  array                           Associative array containing the records/items that reference the id
     */
    public function references(string $record_id, bool $nested = false, ?string $version = null) : array {
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
     * Retrieve/get a specific record/item by id
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/self?language=http
     *
     * @param   string      $record_id      The id of the record/item
     * @param   bool        $nested         Should the returned record contain nested data structures?
     * @param   string      $version        Should we return the 'published' or 'current' versions of the item? Defaults to current.
     *
     * @return  array                       Associative array containing the record/item
     */
    public function retrieve(string $record_id, bool $nested = false, string $version = 'current') : array {
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
     * Delete a specific record/item by id
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/destroy?language=http
     *
     * @param   string      $record_id      The id of the record/item
     *
     * @return  array                       Associative array containing the "job" info that was scheduled to delete this record
     */
    public function delete(string $record_id) : array {
        return $this->handler->execute('DELETE', '/items/' . $record_id);
    }

    /**
     * Publish a specific record/item by id
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/publish?language=http
     *
     * @param   string      $record_id                  The id of the record/item
     * @param   bool        $recursive                  Should we recursively publish non-published items connected to this item?
     * @param   array|null  $locales                    If provided, we will limit publishing to the supplied locales (must set a value for $non_localized_content, too)
     * @param   bool|null   $non_localized_content      If locales are provided, should we publish items with no locales set?
     *
     * @return  array                                   An associative array of the record/item that was published
     */
    public function publish(string $record_id, bool $recursive = false, ?array $locales = null, ?bool $non_localized_content = null) : array {
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
     * Unpublish a specific record/item by id
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/unpublish?language=http
     *
     * @param   string      $record_id      The id of the record/item
     * @param   bool        $recursive      Should we recursively unpublish published items connected to this item?
     * @param   array|null  $locales        If provided, we will limit unpublishing to the supplied locales
     */
    public function unpublish(string $record_id, bool $recursive = false, ?array $locales = null) : array {
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
     * @see https://www.datocms.com/docs/content-management-api/resources/item/bulk_publish?language=http
     *
     * @param   array   $record_ids     A list of ids to publish
     *
     * @return  array                   An associative array containing the "job" info for the job that was scheduled to perform this action
     */
    public function publishBulk(array $record_ids) : array {
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
     * @see https://www.datocms.com/docs/content-management-api/resources/item/bulk_unpublish?language=http
     *
     * @param   array   $record_ids     A list of ids to unpublish
     *
     * @return  array                   An associative array containing the "job" info for the job that was scheduled to perform this action
     */
    public function unpublishBulk(array $record_ids) : array {
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
     * Delete/destroy multiple records/items at once
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/bulk_destroy?language=http
     *
     * @param   array   $record_ids     A list of ids to delete
     *
     * @return  array                   An associative array containing the "job" info for the job that was scheduled to perform this action
     */
    public function deleteBulk(array $record_ids) : array {
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
     * Move multiple records/items to a different "stage"
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/bulk_move_to_stage?language=http
     *
     * @param   array   $record_ids     A list of ids to move
     * @param   string  $stage          The name of the stage to move the records to
     *
     * @return  array                   An associative array containing the "job" info for the job that was scheduled to perform this action
     */
    public function moveToStageBulk(array $record_ids, string $stage) : array {
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