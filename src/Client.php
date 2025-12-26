<?php

namespace DealNews\DatoCMS\CMA;

use DealNews\DatoCMS\CMA\API\Record;
use DealNews\DatoCMS\CMA\API\Upload;
use DealNews\DatoCMS\CMA\API\UploadCollection;
use DealNews\DatoCMS\CMA\API\UploadRequest;
use DealNews\DatoCMS\CMA\API\UploadSmartTag;
use DealNews\DatoCMS\CMA\API\UploadTag;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Main entry point for the DatoCMS Content Management API client
 *
 * Provides access to DatoCMS API endpoints for managing records/items and
 * uploads. Configure via constructor parameters or environment variables
 * (DN_DATOCMS_API_TOKEN, DN_DATOCMS_ENVIRONMENT, DN_DATOCMS_BASE_URL,
 * DN_DATOCMS_LOG_LEVEL).
 *
 * Usage:
 * ```php
 * $client = new Client('your-api-token', 'your-environment');
 * $records = $client->record->list();
 * $upload = $client->upload->uploadFile('/path/to/image.jpg');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api
 */
class Client {

    /**
     * API endpoint for record/item operations
     *
     * @var Record
     */
    public readonly Record $record;

    /**
     * API endpoint for upload operations
     *
     * Includes helper methods uploadFile() and uploadFromUrl() for complete
     * upload workflow.
     *
     * @var Upload
     */
    public readonly Upload $upload;

    /**
     * API endpoint for upload request operations
     *
     * Used to request S3 upload permissions. For most use cases, use
     * $upload->uploadFile() or $upload->uploadFromUrl() instead.
     *
     * @var UploadRequest
     */
    public readonly UploadRequest $upload_request;

    /**
     * API endpoint for upload collection (folder) operations
     *
     * @var UploadCollection
     */
    public readonly UploadCollection $upload_collection;

    /**
     * API endpoint for upload tag operations
     *
     * @var UploadTag
     */
    public readonly UploadTag $upload_tag;

    /**
     * API endpoint for upload smart tag operations (read-only)
     *
     * @var UploadSmartTag
     */
    public readonly UploadSmartTag $upload_smart_tag;

    /**
     * @param string|null          $apiToken    API Token for your DatoCMS project
     * @param string|null          $environment The DatoCMS environment name
     * @param LoggerInterface|null $logger      Optional logger for API requests
     * @param string               $log_level   PSR-3 LogLevel (default: "info")
     * @param string|null          $base_url    Optional custom base URL for proxies
     */
    public function __construct(
        ?string             $apiToken = null,
        ?string             $environment = null,
        ?LoggerInterface    $logger = null,
        string              $log_level = LogLevel::INFO,
        ?string             $base_url = null
    ) {
        $config = Config::init();

        if (!is_null($apiToken)) {
            $config->apiToken = $apiToken;
        }
        if (!is_null($environment)) {
            $config->environment = $environment;
        }
        if (!is_null($logger)) {
            $config->logger = $logger;
        }
        if (!is_null($log_level)) {
            $config->log_level = $log_level;
        }
        if (!is_null($base_url)) {
            $config->base_url = $base_url;
        }

        $this->record = new Record();
        $this->upload = new Upload();
        $this->upload_request = new UploadRequest();
        $this->upload_collection = new UploadCollection();
        $this->upload_tag = new UploadTag();
        $this->upload_smart_tag = new UploadSmartTag();
    }

}