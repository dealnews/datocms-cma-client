<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Parameters\Common as CommonParameter;

/**
 * API handler for DatoCMS upload tag operations
 *
 * Provides CRUD operations for user-defined tags that can be applied to
 * uploads for organization and filtering.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $tags = $client->upload_tag->list();
 * $tag = $client->upload_tag->create('banner');
 * $client->upload_tag->delete($tag['data']['id']);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload-tag
 */
class UploadTag extends Base {

    /**
     * List all upload tags
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-tag/instances
     *
     * @param CommonParameter|null $parameters Optional pagination parameters
     *
     * @return array<string, mixed> The API response body with tag data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(?CommonParameter $parameters = null): array {
        $query_params = !is_null($parameters) ? $parameters->toArray() : [];
        return $this->handler->execute('GET', '/upload-tags', $query_params);
    }

    /**
     * Retrieve a specific upload tag by ID
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-tag/self
     *
     * @param string $tag_id The ID of the upload tag
     *
     * @return array<string, mixed> The tag data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $tag_id): array {
        return $this->handler->execute('GET', '/upload-tags/' . $tag_id);
    }

    /**
     * Create a new upload tag
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-tag/create
     *
     * @param string $name The name of the tag to create
     *
     * @return array<string, mixed> The created tag data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(string $name): array {
        $post_data = [
            'data' => [
                'type'       => 'upload_tag',
                'attributes' => [
                    'name' => $name,
                ],
            ],
        ];

        return $this->handler->execute('POST', '/upload-tags', [], $post_data);
    }

    /**
     * Delete an upload tag
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-tag/destroy
     *
     * @param string $tag_id The ID of the tag to delete
     *
     * @return array<string, mixed> Empty response on success
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $tag_id): array {
        return $this->handler->execute('DELETE', '/upload-tags/' . $tag_id);
    }
}
