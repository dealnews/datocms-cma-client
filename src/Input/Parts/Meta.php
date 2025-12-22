<?php

namespace DealNews\DatoCMS\CMA\Input\Parts;

use Moonspot\ValueObjects\ValueObject;

/**
 * Metadata properties for a DatoCMS record
 *
 * Controls record timestamps, version locking, and workflow stage.
 * Properties set to false (default) are excluded from API output,
 * allowing DatoCMS to set automatic values.
 *
 * Usage:
 * ```php
 * $record->meta->created_at = '2025-01-01T00:00:00Z';
 * $record->meta->stage = 'draft';
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item/create
 */
class Meta extends ValueObject {

    /**
     * Creation timestamp in ISO 8601 format
     *
     * If null, DatoCMS sets this automatically (if not already set).
     *
     * @var string|null
     */
    public ?string $created_at = null;

    /**
     * First publication timestamp in ISO 8601 format
     *
     * - false: DatoCMS sets automatically (excluded from output)
     * - null: Forces DatoCMS to unset an existing value
     * - string: Sets to specific timestamp
     *
     * @var string|null|false
     */
    public string|null|false $first_published_at = false;

    /**
     * Version string for optimistic locking
     *
     * When set, DatoCMS verifies you're updating the expected version.
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/update#optimistic-locking
     *
     * @var string|null
     */
    public ?string $current_version = null;

    /**
     * Workflow stage name
     *
     * - false: DatoCMS sets automatically (excluded from output)
     * - null: Forces DatoCMS to unset an existing value
     * - string: Sets to specific stage
     *
     * @var string|null|false
     */
    public string|null|false $stage = false;

    /**
     * Converts meta to array, excluding unset properties
     *
     * Properties set to false or empty are excluded from output.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Meta array for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['created_at'])) {
            unset($array['created_at']);
        }
        if ($array['first_published_at'] === false) {
            unset($array['first_published_at']);
        }
        if (empty($array['current_version'])) {
            unset($array['current_version']);
        }
        if ($array['stage'] === false) {
            unset($array['stage']);
        }
        return $array;
    }
}