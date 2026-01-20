<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Parameters\UploadTag as UploadTagParameter;

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
     * @param UploadTagParameter|null $parameters Optional pagination
     *                                            parameters
     *
     * @return array<string, mixed> The API response body with tag data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(?UploadTagParameter $parameters = null): array {
        $query_params = !is_null($parameters) ? $parameters->toArray() : [];

        return $this->handler->execute('GET', '/upload-tags', $query_params);
    }

    /**
     * Return all upload tags with automatic pagination
     *
     * Automatically paginates through all upload tags by making multiple API
     * requests with 500-tag chunks. Useful when you need to retrieve an
     * entire dataset without manually managing pagination.
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-tag/instances
     *
     * @param UploadTagParameter|null $parameters Optional parameters for
     *                                            filtering and sorting. Page
     *                                            offset/limit are overridden.
     *
     * @return array<string, mixed> All tags in `['data' => [...]]` format
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function listAll(?UploadTagParameter $parameters = null): array {
        if ($parameters === null) {
            $parameters = new UploadTagParameter();
        } else {
            $parameters = clone $parameters;
        }

        $data   = [];
        $offset = 0;
        $limit  = 500;

        $parameters->page->limit = $limit;

        do {
            $parameters->page->offset = $offset;

            $response = $this->list($parameters);
            $tags     = $response['data'] ?? [];

            $data = array_merge($data, $tags);

            $offset += $limit;
        } while (count($tags) === $limit);

        return ['data' => $data];
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
