<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Relationships;

use Moonspot\ValueObjects\ValueObject;

/**
 * Upload collection relationship
 *
 * Represents a relationship to an upload collection. Can be used as:
 * - A single parent collection relationship
 * - A child collection in an array of children
 * - The collection an upload belongs to
 *
 * The type is always 'upload_collection'.
 *
 * Usage:
 * ```php
 * // Single collection relationship
 * $uploadCollection->relationships->parent->id = 'parent-collection-id';
 *
 * // Upload's collection
 * $upload->relationships->upload_collection->id = 'collection-id';
 * ```
 */
class UploadCollection extends ValueObject {

    /**
     * Relationship type, must always be "upload_collection"
     *
     * WARNING: This property MUST be set to "upload_collection". Setting any other value
     * will cause API errors. Do not modify this property.
     *
     * @var string
     */
    public readonly string $type;

    /**
     * Upload collection ID
     *
     * The unique identifier for the DatoCMS collection.
     *
     * @var string
     */
    public string $id = '';

    public function __construct() {
        $this->type = 'upload_collection';
    }


    /**
     * Converts to API array format
     *
     * Wraps type and id in {data: {...}} structure as required by the API.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Upload collection relationship for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);

        return ['data' => $array];
    }
}
