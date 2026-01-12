<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Parameters\RecordVersion as RecordVersionParameter;

/**
 * API handler for DatoCMS record version operations
 *
 * Provides methods for listing, retrieving, and restoring record versions.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $versions = $client->record_version->list('record-id');
 * $version = $client->record_version->retrieve('record-version-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item-version
 */
class RecordVersion extends Base {

    /**
     * Restore an old record version
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-version/restore
     *
     * @param   string                  $record_version_id      ID of the record version to restore
     *
     * @return  array<string, mixed>                            The information for the "job" that was created to restore this version
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function restore(string $record_version_id): array {
        return $this->handler->execute('POST', '/versions/' . $record_version_id . '/restore');
    }

    /**
     * List all record versions
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item-version/instances
     *
     * @param   string                          $record_id              ID of the record to list versions for
     * @param   RecordVersionParameter|null     $parameters             Optional query parameters for the request
     *
     * @return  array<string, mixed>                                    List of versions of the record
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(string $record_id, ?RecordVersionParameter $parameters = null): array {
        $query_params = !is_null($parameters) ? $parameters->toArray() : [];
        return $this->handler->execute('GET', '/items/' . $record_id . '/versions', $query_params);
    }

    /**
     * Retrieve a record version
     *
     * @param   string                          $record_version_id      The unique identifier of the record version to retrieve.
     * @param   RecordVersionParameter|null     $parameters             Optional parameters to customize the retrieval (get nested structure data).
     *
     * @return array<string, mixed>                                     An associative array containing the details of the retrieved record version.
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $record_version_id, ?RecordVersionParameter $parameters = null): array {
        $query_params = !is_null($parameters) ? $parameters->toArray() : [];
        if (!empty($query_params['page'])) {
            unset($query_params['page']);
        }
        return $this->handler->execute('GET', '/versions/' . $record_version_id, $query_params);
    }
}
