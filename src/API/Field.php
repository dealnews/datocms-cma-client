<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Input\Field as FieldInput;

class Field extends Base {

    /**
     * Create a new field for a model/item-type
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/field/create
     *
     * @param   string                              $model_id_or_api_key    ID or api key of the model/item-type to create the field for
     * @param   array<string, mixed>|FieldInput     $data                   Field data
     *
     * @return array<string, mixed>                                         The created field
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(string $model_id_or_api_key, array|FieldInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        return $this->handler->execute('POST', '/item-types/' . $model_id_or_api_key . '/fields', [], ['data' => $data]);
    }

    /**
     * Update a field
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/field/update
     *
     * @param   string                              $field_id_or_api_key    ID or api key of the field to update
     * @param   array<string, mixed>|FieldInput     $data                   Field data
     *
     * @return array<string, mixed>                                         The information for the "job" that was created to update the field
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function update(string $field_id_or_api_key, array|FieldInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        return $this->handler->execute('PUT', '/fields/' . $field_id_or_api_key, [], ['data' => $data]);
    }

    /**
     * List all fields of a model/block
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/field/instances
     *
     * @param   string                  $model_id_or_api_key    ID or api key of the model/item-type to list fields for
     *
     * @return  array<string, mixed>                            The fields of the model/item-type
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(string $model_id_or_api_key) : array {
        return $this->handler->execute('GET', '/item-types/' . $model_id_or_api_key . '/fields');
    }

    /**
     * List fields referencing a model/block
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/field/referencing
     *
     * @param   string                  $model_id_or_api_key    ID or api key of the model/item-type to list referencing fields for
     *
     * @return  array<string, mixed>                            The fields referencing the model/item-type
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function referencing(string $model_id_or_api_key) : array {
        return $this->handler->execute('GET', '/item-types/' . $model_id_or_api_key . '/fields/referencing');
    }

    /**
     * Retrieve a field
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/field/self
     *
     * @param   string                  $field_id_or_api_key    ID or api key of the field to retrieve
     *
     * @return  array<string, mixed>                            The field
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function find(string $field_id_or_api_key): array {
        return $this->handler->execute('GET', '/fields/' . $field_id_or_api_key);
    }

    /**
     * Delete a field
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/field/destroy
     *
     * @param   string                  $field_id_or_api_key    ID or api key of the field to delete
     *
     * @return  array<string, mixed>                            The information for the "job" that was created to delete the field
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $field_id_or_api_key): array {
        return $this->handler->execute('DELETE', '/fields/' . $field_id_or_api_key);
    }

    /**
     * Duplicate a field
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/field/duplicate
     *
     * @param   string                  $field_id_or_api_key    ID or api key of the field to duplicate
     *
     * @return  array<string, mixed>                            The information for the "job" that was created to duplicate the field
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function duplicate(string $field_id_or_api_key): array {
        return $this->handler->execute('POST', '/fields/' . $field_id_or_api_key . '/duplicate');
    }
}
