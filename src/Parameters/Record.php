<?php

namespace DealNews\DatoCMS\CMA\Parameters;

use DealNews\DatoCMS\CMA\Parameters\Parts\OrderBy;
use DealNews\DatoCMS\CMA\Parameters\Parts\Filter;

/**
 * Query parameters for listing DatoCMS records
 *
 * Provides filtering, sorting, and pagination options for the record list API.
 *
 * Usage:
 * ```php
 * $params = new Record();
 * $params->filter->type = ['article', 'page'];
 * $params->filter->fields->addField('status', 'published', 'eq');
 * $params->order_by->addOrderByField('created_at', 'DESC');
 * $params->page->limit = 50;
 * $records = $client->record->list($params);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item/instances
 */
class Record extends CommonWithLocale {

    /**
     * Include nested data structures in response
     *
     * @var bool
     */
    public bool $nested = false;

    /**
     * Record version to retrieve
     *
     * Valid values: 'published' or 'current'. Enforced by setter.
     *
     * @var string
     */
    public string $version = 'published' {
        set {
            if (!in_array($value, ['published', 'current'])) {
                throw new \InvalidArgumentException('version must be "published" or "current"');
            }
            $this->version = $value;
        }
    }

    /**
     * Sort order specification
     *
     * @var OrderBy
     */
    public OrderBy $order_by;

    /**
     * Filter criteria
     *
     * @var Filter
     */
    public Filter $filter;

    /**
     * Initializes order_by and filter objects
     */
    public function __construct() {
        $this->order_by = new OrderBy();
        $this->filter = new Filter();
        parent::__construct();
    }

    /**
     * Converts parameters to query string array
     *
     * Excludes empty values and formats order_by as comma-separated string.
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
            } elseif ($key === 'order_by') {
                $array['order_by'] = implode(',', $value);
            }
        }
        return $array;
    }
}