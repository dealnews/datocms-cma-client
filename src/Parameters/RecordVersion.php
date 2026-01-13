<?php

namespace DealNews\DatoCMS\CMA\Parameters;

/**
 * Query parameters for listing DatoCMS record versions
 *
 * Provides return-type and pagination options for the record versions list API.
 *
 * Usage:
 * ```php
 * $params = new RecordVersion();
 * $params->page->limit = 50;
 * $versions = $client->record_version->list('record-id', $params);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item-version/instances
 */
class RecordVersion extends Common {

    /**
     * Include nested data structures in response
     *
     * @var bool
     */
    public bool $nested = false;

    /**
     * Converts parameters to query string array
     *
     * Excludes empty values.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Query parameters for API request
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        foreach ($array as $key => $value) {
            if (empty($value)) {
                unset($array[$key]);
            }
        }
        return $array;
    }
}
