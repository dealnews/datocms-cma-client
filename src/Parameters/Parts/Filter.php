<?php

namespace DealNews\DatoCMS\CMA\Parameters\Parts;

use Moonspot\ValueObjects\ValueObject;

/**
 * Filter parameters for record list queries
 *
 * Provides multiple filtering options: by IDs, item types, text query,
 * field values, and validity status.
 *
 * Usage:
 * ```php
 * $params->filter->ids = ['id1', 'id2'];
 * $params->filter->type = ['article', 'page'];
 * $params->filter->query = 'search term';
 * $params->filter->fields->addField('status', 'published', 'eq');
 * $params->filter->only_valid = true;
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item/instances
 */
class Filter extends ValueObject {

    /**
     * Filter by specific record IDs
     *
     * @var array<string>
     */
    public array $ids = [];

    /**
     * Filter by item type (model) IDs
     *
     * @var array<string>
     */
    public array $type = [];

    /**
     * Full-text search query
     *
     * @var string|null
     */
    public ?string $query = null;

    /**
     * Field-level filter conditions
     *
     * @var FilterFields
     */
    public FilterFields $fields;

    /**
     * Only return valid records (all required fields filled)
     *
     * @var bool
     */
    public bool $only_valid = false;

    /**
     * Initializes the fields filter object
     */
    public function __construct() {
        $this->fields = new FilterFields();
    }

    /**
     * Converts filter to query parameters
     *
     * Excludes empty values and joins array values with commas.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Filter query parameters
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        foreach ($array as $key => $value) {
            if (empty($value)) {
                unset($array[$key]);
            } elseif ($key === 'ids' || $key === 'type') {
                $array[$key] = implode(',', $value);
            }
        }

        return $array;
    }
}
