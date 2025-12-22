<?php

namespace DealNews\DatoCMS\CMA\Parameters\Parts;

use Moonspot\ValueObjects\ValueObject;

/**
 * Pagination parameters for record queries
 *
 * Controls result offset and limit. Default values (offset=0, limit=15)
 * are excluded from output to use API defaults.
 *
 * Usage:
 * ```php
 * $params->page->offset = 50;
 * $params->page->limit = 100;
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item/instances
 */
class Page extends ValueObject {

    /**
     * Default offset value (excluded from output when used)
     *
     * @var int
     */
    const int DEFAULT_OFFSET = 0;

    /**
     * Default limit value (excluded from output when used)
     *
     * @var int
     */
    const int DEFAULT_LIMIT = 15;

    /**
     * Number of records to skip
     *
     * @var int
     */
    public int $offset = self::DEFAULT_OFFSET;

    /**
     * Maximum number of records to return
     *
     * @var int
     */
    public int $limit = self::DEFAULT_LIMIT;

    /**
     * Converts pagination to array, excluding default values
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, int> Pagination parameters
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($array['offset'] === self::DEFAULT_OFFSET) {
            unset($array['offset']);
        }
        if ($array['limit'] === self::DEFAULT_LIMIT) {
            unset($array['limit']);
        }
        return $array;
    }
}