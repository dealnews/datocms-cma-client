<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Upload;

use DealNews\DatoCMS\CMA\Input\Parts\Relationships\Creator;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships\UploadCollection;
use Moonspot\ValueObjects\ValueObject;

/**
 * Relationship definitions for a DatoCMS upload
 *
 * Defines the optional upload_collection and creator relationships.
 * Only upload_collection can be set when creating uploads.
 * Both can be optionally set when updating uploads.
 *
 * Usage:
 * ```php
 * $upload->relationships->upload_collection->id = 'collection-id';
 * $upload->relationships->creator->type = 'user';
 * $upload->relationships->creator->id = 'user-id';
 * ```
 */
class Relationships extends ValueObject {

    /**
     * Upload collection relationship
     *
     * Optional. Specifies which collection this upload belongs to.
     * Can be set when creating or updating uploads.
     * Excluded from output if id is not set.
     *
     * @var UploadCollection
     */
    public UploadCollection $upload_collection;

    /**
     * Creator relationship
     *
     * Optional. Specifies who created the upload.
     * Can only be set when updating uploads (not when creating).
     * Excluded from output if type and id are not both set.
     *
     * @var Creator
     */
    public Creator $creator;

    /**
     * Initializes relationship objects
     */
    public function __construct() {
        $this->upload_collection = new UploadCollection();
        $this->creator           = new Creator();
    }

    /**
     * Converts relationships to array, excluding empty relationships
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Relationships array for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);

        // Remove upload_collection if id is empty
        if (empty($array['upload_collection']['data']['id'])) {
            unset($array['upload_collection']);
        }

        // Remove creator if it returns empty array (both type and id not set)
        if (empty($array['creator'])) {
            unset($array['creator']);
        }

        return $array;
    }
}
