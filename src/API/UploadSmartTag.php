<?php

namespace DealNews\DatoCMS\CMA\API;

/**
 * API handler for DatoCMS upload smart tag operations
 *
 * Provides read-only access to smart tags that DatoCMS auto-detects from
 * uploaded images (e.g., 'person', 'outdoor', 'text', etc.).
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $smart_tags = $client->upload_smart_tag->list();
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload-smart-tag
 */
class UploadSmartTag extends Base {

    /**
     * List all upload smart tags
     *
     * Returns all auto-detected smart tags across all uploads.
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-smart-tag/instances
     *
     * @return array<string, mixed> The API response body with smart tag data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(): array {
        return $this->handler->execute('GET', '/upload-smart-tags');
    }
}
