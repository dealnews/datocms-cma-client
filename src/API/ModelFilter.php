<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Input\ModelFilter as ModelFilterInput;

/**
 * API handler for DatoCMS model filter operations
 *
 * Model filters are saved searches that help editors quickly find records
 * within a model. Provides methods for CRUD operations on filters.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $filters = $client->model_filter->list();
 * $filter = $client->model_filter->retrieve('filter-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item-type-filter
 */
class ModelFilter extends Base {

    /**
     * Return a list of all model filters
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type-filter/instances
     *
     * @return array<string, mixed> The API response body decoded as an
     *                              associative array
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(): array {
        return $this->handler->execute('GET', '/item-type-filters');
    }

    /**
     * Retrieve a specific model filter by ID
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type-filter/self
     *
     * @param string $filter_id The ID of the filter
     *
     * @return array<string, mixed> The filter data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $filter_id): array {
        return $this->handler->execute('GET', '/item-type-filters/' . $filter_id);
    }

    /**
     * Create a new model filter
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type-filter/create
     *
     * @param array<string, mixed>|ModelFilterInput $data Filter data; method
     *                                                    auto-wraps in {data: ...}
     *
     * @return array<string, mixed> The created filter
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(array|ModelFilterInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute(
            'POST',
            '/item-type-filters',
            [],
            ['data' => $data]
        );
    }

    /**
     * Update an existing model filter
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type-filter/update
     *
     * @param string                                $filter_id The ID of the filter to update
     * @param array<string, mixed>|ModelFilterInput $data      Updated filter data; method
     *                                                         auto-wraps in {data: ...}
     *
     * @return array<string, mixed> The updated filter
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function update(string $filter_id, array|ModelFilterInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute(
            'PUT',
            '/item-type-filters/' . $filter_id,
            [],
            ['data' => $data]
        );
    }

    /**
     * Delete a specific model filter by ID
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type-filter/destroy
     *
     * @param string $filter_id The ID of the filter to delete
     *
     * @return array<string, mixed> The deletion response
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $filter_id): array {
        return $this->handler->execute('DELETE', '/item-type-filters/' . $filter_id);
    }
}
