<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Upload;

use Moonspot\ValueObjects\ValueObject;

/**
 * Attributes for DatoCMS upload create/update operations
 *
 * Contains all attribute fields for an upload entity including path, copyright,
 * author, notes, tags, and localized default field metadata.
 *
 * Usage:
 * ```php
 * $attributes = new Attributes();
 * $attributes->path = '/45/1496845848-image.jpg';
 * $attributes->author = 'John Doe';
 * $attributes->tags = ['hero', 'banner'];
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload/create
 */
class Attributes extends ValueObject {

    /**
     * Upload path from the upload request response
     *
     * Required when creating an upload. Format: /{id}/{timestamp}-{filename}
     *
     * @var string|null
     */
    public ?string $path = null;

    /**
     * Copyright information for the asset
     *
     * @var string|null
     */
    public ?string $copyright = null;

    /**
     * Author or creator of the asset
     *
     * @var string|null
     */
    public ?string $author = null;

    /**
     * Internal notes about the asset
     *
     * @var string|null
     */
    public ?string $notes = null;

    /**
     * Tags for organizing and filtering uploads
     *
     * @var array<string>
     */
    public array $tags = [];

    /**
     * Default metadata values per locale
     *
     * Used to set default alt text, title, focal point, and custom data
     * that will be applied when the upload is used in records.
     *
     * @var DefaultFieldMetadata
     */
    public DefaultFieldMetadata $default_field_metadata;

    /**
     * Initializes the attributes with default values
     */
    public function __construct() {
        $this->default_field_metadata = new DefaultFieldMetadata();
    }

    /**
     * Converts attributes to array for API submission
     *
     * Excludes empty/null values to minimize payload size.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Attributes array
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);

        // Remove null/empty values
        if (!isset($array['path']) || is_null($array['path'])) {
            unset($array['path']);
        }
        if (!isset($array['copyright']) || is_null($array['copyright'])) {
            unset($array['copyright']);
        }
        if (!isset($array['author']) || is_null($array['author'])) {
            unset($array['author']);
        }
        if (!isset($array['notes']) || is_null($array['notes'])) {
            unset($array['notes']);
        }
        if (!isset($array['tags']) || empty($array['tags'])) {
            unset($array['tags']);
        }
        if (!isset($array['default_field_metadata']) || empty($array['default_field_metadata'])) {
            unset($array['default_field_metadata']);
        }

        return $array;
    }
}
