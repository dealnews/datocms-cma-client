<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Input\FieldSet as FieldSetInput;

/**
 * API handler for DatoCMS fieldset operations
 *
 * Provides methods for all fieldset-related CRUD operations including listing,
 * creating, updating, and deleting fieldsets.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $fieldset_info = $client->fieldset->retrieve('fieldset-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/fieldset
 */
class FieldSet extends Base {

    /**
     * Create a new fieldset
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/fieldset/create
     *
     * @param   string                                  $model_id_or_api_key    ID or API key of the model to create the fieldset for
     * @param   array<string, mixed>|FieldSetInput      $data                   Fieldset data
     *
     * @return array<string, mixed>                                             The created fieldset
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(string $model_id_or_api_key, array|FieldSetInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute('POST', '/item-types/' . $model_id_or_api_key . '/fieldsets', [], ['data' => $data]);
    }

    /**
     * Update a fieldset
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/fieldset/update
     *
     * @param string                                  $fieldset_id            ID of the fieldset to update
     * @param array<string, mixed>|FieldSetInput      $data                   Fieldset data
     *
     * @return array<string, mixed>                                           The updated fieldset
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function update(string $fieldset_id, array|FieldSetInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute('PUT', '/fieldsets/' . $fieldset_id, [], ['data' => $data]);
    }

    /**
     * List all fieldsets of a model/block
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/fieldset/instances
     *
     * @param   string                  $model_id_or_api_key    ID or API key of the model to list fieldsets for
     *
     * @return  array<string, mixed>                            The list of fieldsets
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(string $model_id_or_api_key): array {
        return $this->handler->execute('GET', '/item-types/' . $model_id_or_api_key . '/fieldsets');
    }

    /**
     * Retrieve a fieldset
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/fieldset/self
     *
     * @param   string                  $fieldset_id            ID of the fieldset to retrieve
     *
     * @return  array<string, mixed>                            The retrieved fieldset
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $fieldset_id): array {
        return $this->handler->execute('GET', '/fieldsets/' . $fieldset_id);
    }

    /**
     * Delete a fieldset
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/fieldset/destroy
     *
     * @param   string                  $fieldset_id            ID of the fieldset to delete
     *
     * @return  array<string, mixed>                            The deleted fieldset
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $fieldset_id): array {
        return $this->handler->execute('DELETE', '/fieldsets/' . $fieldset_id);
    }
}
