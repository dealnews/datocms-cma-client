<?php

namespace DealNews\DatoCMS\CMA\Parameters;

use Moonspot\ValueObjects\ValueObject;

/**
 * Query parameters for retrieving site information
 *
 * Provides options for how you would like to receive the data
 *
 * Usage:
 * ```php
 * $params = new Site();
 * $params->include = ['item_types', 'account'];
 * $site_info = $client->site->find($params);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/site/self#query-parameters
 */
class Site extends ValueObject {

    /**
     * A list of relationship paths for "relationships" you would like to include in the response.
     *
     * A relationship path is a dot-separated list of relationship names.
     *
     * Possible values:
     *  - item_types
     *  - item_types.fields
     *  - item_types.fieldsets
     *  - item_types.singleton_item
     *  - account
     */
    public array $include = [];

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