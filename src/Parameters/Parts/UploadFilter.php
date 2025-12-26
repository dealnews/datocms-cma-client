<?php

namespace DealNews\DatoCMS\CMA\Parameters\Parts;

use Moonspot\ValueObjects\ValueObject;

/**
 * Filter parameters for upload list queries
 *
 * Provides filtering options specific to uploads: by IDs, file types, text
 * query, collection, and various upload-specific criteria.
 *
 * Usage:
 * ```php
 * $params->filter->ids = ['id1', 'id2'];
 * $params->filter->type = 'image';
 * $params->filter->query = 'banner';
 * $params->filter->upload_collection_id = 'collection-123';
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload/instances
 */
class UploadFilter extends ValueObject {

    /**
     * Filter by specific upload IDs
     *
     * @var array<string>
     */
    public array $ids = [];

    /**
     * Filter by file type
     *
     * Valid values: 'image', 'video', 'audio', 'richtext', 'presentation',
     * 'spreadsheet', 'pdfdocument', 'archive', 'unknown'
     *
     * @var string|null
     */
    public ?string $type = null;

    /**
     * Full-text search query
     *
     * Searches across filename, title, alt text, notes, and tags.
     *
     * @var string|null
     */
    public ?string $query = null;

    /**
     * Filter by upload collection ID
     *
     * @var string|null
     */
    public ?string $upload_collection_id = null;

    /**
     * Filter by uploads with specific smart tags
     *
     * @var array<string>
     */
    public array $smart_tags = [];

    /**
     * Filter by uploads with specific user tags
     *
     * @var array<string>
     */
    public array $tags = [];

    /**
     * Filter by author
     *
     * @var string|null
     */
    public ?string $author = null;

    /**
     * Filter by copyright
     *
     * @var string|null
     */
    public ?string $copyright = null;

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
            } elseif ($key === 'ids' || $key === 'smart_tags' || $key === 'tags') {
                $array[$key] = implode(',', $value);
            }
        }
        return $array;
    }
}
