<?php

namespace DealNews\DatoCMS\CMA\Parameters\Parts;

use Moonspot\ValueObjects\ValueObject;

/**
 * Field-level filter conditions for record queries
 *
 * Builds filter conditions for specific fields using comparison operators.
 *
 * Supported operators include: eq, neq, lt, lte, gt, gte, exists, matches, etc.
 *
 * Usage:
 * ```php
 * $params->filter->fields->addField('status', 'published', 'eq');
 * $params->filter->fields->addField('created_at', '2025-01-01', 'gt');
 * $params->filter->fields->addField('title', 'Hello', 'matches');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item/instances
 */
class FilterFields extends ValueObject {

    /**
     * Field filter conditions keyed by field name and operator
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $fields = [];

    /**
     * Adds a filter condition for a field
     *
     * @param string $field_name Field API name
     * @param mixed  $value      Value to compare against
     * @param string $operator   Comparison operator (default: 'eq')
     *
     * @return FilterFields This instance for method chaining
     */
    public function addField(
        string $field_name,
        mixed $value,
        string $operator = 'eq'
    ): FilterFields {
        $this->fields[$field_name][$operator] = $value;

        return $this;
    }

    /**
     * Converts field filters to array format
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, array<string, mixed>> Field filter conditions
     */
    public function toArray(?array $data = null): array {
        return $data ?? $this->fields;
    }
}
