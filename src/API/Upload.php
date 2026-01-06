<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Config;
use DealNews\DatoCMS\CMA\Exception\S3Upload;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\Upload as UploadInput;
use DealNews\DatoCMS\CMA\Parameters\Upload as UploadParameter;
use GuzzleHttp\Client;

/**
 * API handler for DatoCMS upload operations
 *
 * Provides methods for all upload-related CRUD operations including listing,
 * creating, updating, deleting, and bulk operations. Also includes helper
 * methods for the complete upload workflow (upload to S3 and register).
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $uploads = $client->upload->list();
 * $upload = $client->upload->uploadFile('/path/to/image.jpg');
 * $upload = $client->upload->uploadFromUrl('https://example.com/image.jpg');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload
 */
class Upload extends Base {

    /**
     * API handler for upload requests (S3 permission)
     *
     * @var UploadRequest
     */
    protected UploadRequest $upload_request;

    /**
     * Guzzle client for S3 uploads
     *
     * @var Client|null
     */
    protected ?Client $s3_client = null;

    /**
     * Initializes the Upload API handler
     *
     * @param Handler|null       $handler        Optional pre-configured HTTP handler
     * @param UploadRequest|null $upload_request Optional upload request handler (for testing)
     * @param Client|null        $s3_client      Optional Guzzle client for S3 (for testing)
     */
    public function __construct(
        ?Handler $handler = null,
        ?UploadRequest $upload_request = null,
        ?Client $s3_client = null
    ) {
        parent::__construct($handler);
        $this->upload_request = $upload_request ?? new UploadRequest($this->handler);
        $this->s3_client = $s3_client;
    }

    /**
     * Return a list of uploads
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload/instances
     *
     * @param UploadParameter|null $parameters Optional parameters for filtering,
     *                                          sorting, and pagination
     *
     * @return array<string, mixed> The API response body decoded as an
     *                              associative array
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(?UploadParameter $parameters = null): array {
        $query_params = !is_null($parameters) ? $parameters->toArray() : [];
        return $this->handler->execute('GET', '/uploads', $query_params);
    }

    /**
     * Retrieve a specific upload by ID
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload/self
     *
     * @param string $upload_id The ID of the upload
     *
     * @return array<string, mixed> The upload data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $upload_id): array {
        return $this->handler->execute('GET', '/uploads/' . $upload_id);
    }

    /**
     * Create/register a new upload
     *
     * Call this after uploading the file to S3 via the upload request flow.
     * For a simpler workflow, use uploadFile() or uploadFromUrl() instead.
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload/create
     *
     * @param array<string, mixed>|UploadInput $data Upload data with path from S3
     *
     * @return array<string, mixed> The created upload data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(array|UploadInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        return $this->handler->execute('POST', '/uploads', [], ['data' => $data]);
    }

    /**
     * Update an existing upload's metadata
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload/update
     *
     * @param string                           $upload_id The upload ID
     * @param array<string, mixed>|UploadInput $data      Updated metadata
     *
     * @return array<string, mixed> The updated upload data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function update(string $upload_id, array|UploadInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        return $this->handler->execute('PUT', '/uploads/' . $upload_id, [], ['data' => $data]);
    }

    /**
     * Delete an upload
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload/destroy
     *
     * @param string $upload_id The ID of the upload to delete
     *
     * @return array<string, mixed> Empty response on success
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $upload_id): array {
        return $this->handler->execute('DELETE', '/uploads/' . $upload_id);
    }

    /**
     * Get records that reference this upload
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload/references
     *
     * @param string $upload_id The ID of the upload
     *
     * @return array<string, mixed> Records referencing this upload
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function references(string $upload_id): array {
        return $this->handler->execute('GET', '/uploads/' . $upload_id . '/references');
    }

    /**
     * Delete multiple uploads at once
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload/bulk_destroy
     *
     * @param array<string> $upload_ids List of upload IDs to delete
     *
     * @return array<string, mixed> Job info for the scheduled bulk deletion
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function deleteBulk(array $upload_ids): array {
        $post_data = [
            'data' => [
                'type'          => 'upload_bulk_destroy_operation',
                'relationships' => [
                    'uploads' => [
                        'data' => [],
                    ],
                ],
            ],
        ];

        foreach ($upload_ids as $upload_id) {
            $post_data['data']['relationships']['uploads']['data'][] = [
                'type' => 'upload',
                'id'   => $upload_id,
            ];
        }

        return $this->handler->execute('POST', '/uploads/bulk/destroy', [], $post_data);
    }

    /**
     * Update metadata for multiple uploads at once
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/upload/bulk_update
     *
     * @param array<string>        $upload_ids List of upload IDs to update
     * @param array<string, mixed> $attributes Attributes to set on all uploads
     *
     * @return array<string, mixed> Job info for the scheduled bulk update
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function updateBulk(array $upload_ids, array $attributes): array {
        $post_data = [
            'data' => [
                'type'          => 'upload_bulk_update_operation',
                'attributes'    => $attributes,
                'relationships' => [
                    'uploads' => [
                        'data' => [],
                    ],
                ],
            ],
        ];

        foreach ($upload_ids as $upload_id) {
            $post_data['data']['relationships']['uploads']['data'][] = [
                'type' => 'upload',
                'id'   => $upload_id,
            ];
        }

        return $this->handler->execute('PUT', '/uploads/bulk/update', [], $post_data);
    }

    /**
     * Upload a local file to DatoCMS
     *
     * Handles the complete upload flow: requests S3 permission, uploads the
     * file to S3, and registers the upload in DatoCMS.
     *
     * @param string                    $filepath             Path to the local file
     * @param array<string, mixed>|null $metadata             Optional metadata (author,
     *                                                        copyright, notes, tags)
     * @param string|null               $upload_collection_id Optional collection to add to
     *
     * @return array<string, mixed> The created upload data (raw API response)
     *
     * @throws \InvalidArgumentException                   If file doesn't exist or isn't readable
     * @throws \DealNews\DatoCMS\CMA\Exception\S3Upload    On S3 upload failure
     * @throws \DealNews\DatoCMS\CMA\Exception\API         On DatoCMS API error
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode      On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown     On unexpected errors
     */
    public function uploadFile(
        string $filepath,
        ?array $metadata = null,
        ?string $upload_collection_id = null
    ): array {
        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException("File does not exist: $filepath");
        }
        if (!is_readable($filepath)) {
            throw new \InvalidArgumentException("File is not readable: $filepath");
        }

        $filename = basename($filepath);

        // Step 1: Request upload permission
        $request_response = $this->upload_request->create($filename);
        $s3_url = $request_response['data']['attributes']['url'];
        $s3_headers = $request_response['data']['attributes']['request_headers'] ?? [];

        // Step 2: Upload to S3
        $this->uploadToS3($s3_url, $filepath, $s3_headers);

        // Step 3: Register the upload in DatoCMS
        $upload_input = $this->buildUploadInput(
            $request_response['data']['id'],
            $metadata,
            $upload_collection_id
        );

        return $this->create($upload_input);
    }

    /**
     * Upload a file from a remote URL to DatoCMS
     *
     * Downloads the file to a temporary location, then handles the complete
     * upload flow: requests S3 permission, uploads to S3, and registers in DatoCMS.
     *
     * @param string                    $url                  URL of the file to upload
     * @param string|null               $filename             Optional filename override
     * @param array<string, mixed>|null $metadata             Optional metadata
     * @param string|null               $upload_collection_id Optional collection to add to
     *
     * @return array<string, mixed> The created upload data (raw API response)
     *
     * @throws \InvalidArgumentException                   If URL is invalid or download fails
     * @throws \DealNews\DatoCMS\CMA\Exception\S3Upload    On S3 upload failure
     * @throws \DealNews\DatoCMS\CMA\Exception\API         On DatoCMS API error
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode      On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown     On unexpected errors
     */
    public function uploadFromUrl(
        string $url,
        ?string $filename = null,
        ?array $metadata = null,
        ?string $upload_collection_id = null
    ): array {
        // Determine filename from URL if not provided
        if (empty($filename)) {
            $parsed_url = parse_url($url);
            $path = $parsed_url['path'] ?? '';
            $filename = basename($path);
            if (empty($filename)) {
                $filename = 'downloaded_file';
            }
        }

        // Download to temp file
        $temp_file = $this->downloadToTemp($url, $filename);

        try {
            $result = $this->uploadFile($temp_file, $metadata, $upload_collection_id);
        } finally {
            // Clean up temp file
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }
        }

        /** @phan-suppress-next-line PhanPossiblyUndeclaredVariable */
        return $result;
    }

    /**
     * Uploads file contents to S3
     *
     * @param string               $s3_url   The S3 URL to upload to
     * @param string               $filepath Path to the local file
     * @param array<string, mixed> $headers  Headers required for S3 upload
     *
     * @return void
     *
     * @throws S3Upload On upload failure
     */
    protected function uploadToS3(string $s3_url, string $filepath, array $headers): void {
        $client = $this->getS3Client();

        $file_contents = file_get_contents($filepath);
        if ($file_contents === false) {
            throw new S3Upload("Failed to read file: $filepath", 0);
        }

        try {
            $response = $client->request('PUT', $s3_url, [
                'headers' => $headers,
                'body'    => $file_contents,
            ]);
        } catch (\Throwable $e) {
            throw new S3Upload(
                'S3 upload failed: ' . $e->getMessage(),
                0,
                $e
            );
        }

        $status_code = $response->getStatusCode();
        if ($status_code < 200 || $status_code > 299) {
            throw new S3Upload(
                'S3 upload failed with status ' . $status_code,
                $status_code,
                null,
                $response->getBody()->getContents()
            );
        }
    }

    /**
     * Downloads a remote file to a temporary location
     *
     * @param string $url      URL to download from
     * @param string $filename Filename to use for temp file
     *
     * @return string Path to the temporary file
     *
     * @throws \InvalidArgumentException On download failure
     */
    protected function downloadToTemp(string $url, string $filename): string {
        $temp_dir = sys_get_temp_dir();
        $temp_file = $temp_dir . DIRECTORY_SEPARATOR . uniqid('datocms_') . '_' . $filename;

        $client = $this->getS3Client();

        try {
            $response = $client->request('GET', $url, [
                'sink' => $temp_file,
            ]);
        } catch (\Throwable $e) {
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }
            throw new \InvalidArgumentException(
                'Failed to download file from URL: ' . $e->getMessage(),
                0,
                $e
            );
        }

        $status_code = $response->getStatusCode();
        if ($status_code < 200 || $status_code > 299) {
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }
            throw new \InvalidArgumentException(
                'Failed to download file, HTTP status: ' . $status_code
            );
        }

        return $temp_file;
    }

    /**
     * Builds an UploadInput object for registering an upload
     *
     * @param string                    $path                 The S3 path from upload request
     * @param array<string, mixed>|null $metadata             Optional metadata
     * @param string|null               $upload_collection_id Optional collection ID
     *
     * @return UploadInput Configured upload input
     */
    protected function buildUploadInput(
        string $path,
        ?array $metadata,
        ?string $upload_collection_id
    ): UploadInput {
        $upload = new UploadInput();
        $upload->attributes->path = $path;

        if (!empty($metadata)) {
            if (isset($metadata['author'])) {
                $upload->attributes->author = $metadata['author'];
            }
            if (isset($metadata['copyright'])) {
                $upload->attributes->copyright = $metadata['copyright'];
            }
            if (isset($metadata['notes'])) {
                $upload->attributes->notes = $metadata['notes'];
            }
            if (isset($metadata['tags']) && is_array($metadata['tags'])) {
                $upload->attributes->tags = $metadata['tags'];
            }
            if (isset($metadata['default_field_metadata']) && is_array($metadata['default_field_metadata'])) {
                foreach ($metadata['default_field_metadata'] as $locale => $locale_metadata) {
                    $upload->attributes->default_field_metadata->addLocale(
                        $locale,
                        $locale_metadata['alt'] ?? null,
                        $locale_metadata['title'] ?? null,
                        $locale_metadata['focal_point'] ?? null,
                        $locale_metadata['custom_data'] ?? null
                    );
                }
            }
        }

        if (!empty($upload_collection_id)) {
            $upload->upload_collection_id = $upload_collection_id;
        }

        return $upload;
    }

    /**
     * Gets the Guzzle client for S3/HTTP operations
     *
     * @return Client Guzzle HTTP client
     */
    protected function getS3Client(): Client {
        if (is_null($this->s3_client)) {
            $this->s3_client = new Client([
                'http_errors' => false,
            ]);
        }
        return $this->s3_client;
    }
}
