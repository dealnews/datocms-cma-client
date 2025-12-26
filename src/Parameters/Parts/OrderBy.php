<?php

namespace DealNews\DatoCMS\CMA\Parameters\Parts;

use Moonspot\ValueObjects\ValueObject;

/**
 * Sort order specification for record queries
 *
 * Builds ordering criteria with field names and directions (ASC/DESC).
 *
 * Usage:
 * ```php
 * $params->order_by->addOrderByField('created_at', 'DESC');
 * $params->order_by->addOrderByField('title', 'ASC');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item/instances
 */
class OrderBy extends ValueObject {

    /**
     * Order specifications keyed by field name
     *
     * @var array<string, string>
     */
    protected array $order_by = [];

    /**
     * Adds a field to the sort order
     *
     * @param string $name      Field API name
     * @param string $direction Sort direction: 'ASC' or 'DESC'
     *
     * @return OrderBy This instance for method chaining
     *
     * @throws \InvalidArgumentException If direction is not ASC or DESC
     */
    public function addOrderByField(string $name, string $direction): OrderBy {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException('OrderBy direction must be ASC or DESC');
        }

        $this->order_by[$name] = $direction;

        return $this;
    }

    /**
     * Converts order_by to array of formatted strings
     *
     * Returns array like ['field_ASC', 'other_field_DESC'].
     *
     * @param array<string, string>|null $data Optional data override
     *
     * @return array<string> Formatted order_by values
     */
    public function toArray(?array $data = null): array {
        $order_by = $data ?? $this->order_by;
        $order_by_set = [];
        foreach ($order_by as $field_name => $direction) {
            $order_by_set[] = $field_name . '_' . strtoupper($direction);
        }
        return $order_by_set;
    }
}