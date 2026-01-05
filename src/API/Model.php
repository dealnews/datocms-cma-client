<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Parameters\Model as ModelParameter;
use DealNews\DatoCMS\CMA\Input\Model as ModelInput;

/**
 * API handler for DatoCMS model (item-type) operations
 *
 * Provides methods for all model-related CRUD operations including listing,
 * creating, updating, deleting, and duplicating models.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $models = $client->model->list();
 * $model = $client->model->retrieve('model-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item-type
 */
class Model extends Base {

    /**
     * Return a list of models (item-types)
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type/instances
     *
     * @param ModelParameter|null $parameters Optional parameters for pagination
     *
     * @return array<string, mixed> The API response body decoded as an
     *                              associative array
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(?ModelParameter $parameters = null): array {
        $query_params = !is_null($parameters) ? $parameters->toArray() : [];
        return $this->handler->execute('GET', '/item-types', $query_params);
    }

    /**
     * Retrieve a specific model (item-type) by ID
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type/self
     *
     * @param string $model_id The ID of the model
     *
     * @return array<string, mixed> The model data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $model_id): array {
        return $this->handler->execute('GET', '/item-types/' . $model_id);
    }

    /**
     * Create a new model (item-type)
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type/create
     *
     * @param array<string, mixed>|ModelInput $data Model data; method
     *                                              auto-wraps in {data: ...}
     *
     * @return array<string, mixed> The created model
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(array|ModelInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        return $this->handler->execute('POST', '/item-types', [], ['data' => $data]);
    }

    /**
     * Update an existing model (item-type)
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type/update
     *
     * @param string                      $model_id The ID of the model to update
     * @param array<string, mixed>|ModelInput $data Updated model data; method
     *                                              auto-wraps in {data: ...}
     *
     * @return array<string, mixed> The updated model
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function update(string $model_id, array|ModelInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        return $this->handler->execute('PUT', '/item-types/' . $model_id, [], ['data' => $data]);
    }

    /**
     * Delete a specific model (item-type) by ID
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type/destroy
     *
     * @param string $model_id The ID of the model to delete
     *
     * @return array<string, mixed> The deletion response
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $model_id): array {
        return $this->handler->execute('DELETE', '/item-types/' . $model_id);
    }

    /**
     * Create a duplicate of a model (item-type)
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-type/duplicate
     *
     * @param string $model_id The ID of the model to duplicate
     *
     * @return array<string, mixed> The duplicated model
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function duplicate(string $model_id): array {
        return $this->handler->execute('POST', '/item-types/' . $model_id . '/duplicate');
    }
}
