<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Input\UploadCollection as UploadCollectionInput;
use DealNews\DatoCMS\CMA\Parameters\UploadCollection as UploadCollectionParameter;

/**
 * API handler for DatoCMS upload collection operations
 *
 * Provides CRUD operations for upload collections (folders) used to organize
 * uploads in the Media Area.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $collections = $client->upload_collection->list();
 * $collection = $client->upload_collection->create($input);
 * $client->upload_collection->delete($collection['data']['id']);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload-collection
 */
class UploadCollection extends Base {

    /**
     * List all upload collections
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-collection/instances
     *
     * @param UploadCollectionParameter|null $parameters Optional pagination
     *                                                    parameters
     *
     * @return array<string, mixed> The API response body with collection data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(?UploadCollectionParameter $parameters = null): array {
        $query_params = !is_null($parameters) ? $parameters->toArray() : [];

        return $this->handler->execute('GET', '/upload-collections', $query_params);
    }

    /**
     * Return all upload collections with automatic pagination
     *
     * Automatically paginates through all upload collections by making
     * multiple API requests with 500-collection chunks. Useful when you need
     * to retrieve an entire dataset without manually managing pagination.
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-collection/instances
     *
     * @param UploadCollectionParameter|null $parameters Optional parameters
     *                                                    for filtering and
     *                                                    sorting. Page
     *                                                    offset/limit are
     *                                                    overridden.
     *
     * @return array<string, mixed> All collections in `['data' => [...]]`
     *                              format
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function listAll(
        ?UploadCollectionParameter $parameters = null
    ): array {
        if ($parameters === null) {
            $parameters = new UploadCollectionParameter();
        } else {
            $parameters = clone $parameters;
        }

        $data   = [];
        $offset = 0;
        $limit  = 500;

        $parameters->page->limit = $limit;

        do {
            $parameters->page->offset = $offset;

            $response    = $this->list($parameters);
            $collections = $response['data'] ?? [];

            $data = array_merge($data, $collections);

            $offset += $limit;
        } while (count($collections) === $limit);

        return ['data' => $data];
    }

    /**
     * Retrieve a specific upload collection by ID
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-collection/self
     *
     * @param string $collection_id The ID of the collection
     *
     * @return array<string, mixed> The collection data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $collection_id): array {
        return $this->handler->execute('GET', '/upload-collections/' . $collection_id);
    }

    /**
     * Create a new upload collection
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-collection/create
     *
     * @param array<string, mixed>|UploadCollectionInput $data Collection data
     *
     * @return array<string, mixed> The created collection data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(array|UploadCollectionInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute('POST', '/upload-collections', [], ['data' => $data]);
    }

    /**
     * Update an existing upload collection
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-collection/update
     *
     * @param string                                    $collection_id The collection ID
     * @param array<string, mixed>|UploadCollectionInput $data         Updated data
     *
     * @return array<string, mixed> The updated collection data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function update(string $collection_id, array|UploadCollectionInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute('PUT', '/upload-collections/' . $collection_id, [], ['data' => $data]);
    }

    /**
     * Delete an upload collection
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-collection/destroy
     *
     * @param string $collection_id The ID of the collection to delete
     *
     * @return array<string, mixed> Empty response on success
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $collection_id): array {
        return $this->handler->execute('DELETE', '/upload-collections/' . $collection_id);
    }
}
