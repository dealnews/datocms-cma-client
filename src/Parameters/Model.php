<?php

namespace DealNews\DatoCMS\CMA\Parameters;

/**
 * Query parameters for listing DatoCMS models (item-types)
 *
 * Provides filtering and pagination options for the model list API.
 *
 * Usage:
 * ```php
 * $params = new Model();
 * $params->page->limit = 50;
 * $models = $client->model->list($params);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item-type/instances
 */
class Model extends Common {

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
