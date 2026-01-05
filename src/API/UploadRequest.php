<?php

namespace DealNews\DatoCMS\CMA\API;

/**
 * API handler for DatoCMS upload request operations
 *
 * Handles requesting upload permissions from DatoCMS. The upload request
 * returns a temporary S3 URL and headers needed to upload a file directly
 * to S3 before registering it with the Upload API.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $request = $client->upload_request->create('image.jpg');
 * // $request contains 'url' and 'request_headers' for S3 upload
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload-request
 */
class UploadRequest extends Base {

    /**
     * Request permission to upload a file to S3
     *
     * Returns the S3 URL and required headers for uploading a file directly
     * to DatoCMS storage. After uploading to S3, use Upload->create() to
     * register the upload in DatoCMS.
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload-request/create
     *
     * @param string $filename The filename to upload (used for MIME type detection)
     *
     * @return array<string, mixed> Contains 'data' with 'id', 'type', and
     *                              'attributes' including 'url' and 'request_headers'
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(string $filename): array {
        $post_data = [
            'data' => [
                'type'       => 'upload_request',
                'attributes' => [
                    'filename' => $filename,
                ],
            ],
        ];

        return $this->handler->execute('POST', '/upload-requests', [], $post_data);
    }
}
