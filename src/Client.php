<?php

namespace DealNews\DatoCMS\CMA;

use DealNews\DatoCMS\CMA\API\Model;
use DealNews\DatoCMS\CMA\API\Record;
use DealNews\DatoCMS\CMA\API\Upload;
use DealNews\DatoCMS\CMA\API\UploadCollection;
use DealNews\DatoCMS\CMA\API\UploadRequest;
use DealNews\DatoCMS\CMA\API\UploadSmartTag;
use DealNews\DatoCMS\CMA\API\UploadTag;
use DealNews\DatoCMS\CMA\API\ScheduledUnpublishing;
use DealNews\DatoCMS\CMA\API\ScheduledPublication;
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
 *
 * @property-read   Record                      $record                         API endpoint for record/item operations
 * @property-read   Model                       $model                          API endpoint for model/item-type operations
 * @property-read   Upload                      $upload                         API endpoint for upload operations
 * @property-read   UploadRequest               $upload_request                 API endpoint for upload request operations
 * @property-read   UploadCollection            $upload_collection              API endpoint for upload collection (folder) operations
 * @property-read   UploadTag                   $upload_tag                     API endpoint for upload tag operations
 * @property-read   UploadSmartTag              $upload_smart_tag               API endpoint for upload smart tag operations (read-only)
 * @property-read   ScheduledUnpublishing       $scheduled_unpublishing         API endpoint for scheduling unpublishing operations
 * @property-read   ScheduledPublication        $scheduled_publication          API endpoint for scheduled publication operations
 */
class Client {

    /**
     * API endpoint for record/item operations
     *
     * @var Record
     */
    protected Record $record;

    /**
     * API endpoint for model/item-type operations
     *
     * @var Model
     */
    protected Model $model;

    /**
     * API endpoint for upload operations
     *
     * Includes helper methods uploadFile() and uploadFromUrl() for complete
     * upload workflow.
     *
     * @var Upload
     */
    protected Upload $upload;

    /**
     * API endpoint for upload request operations
     *
     * Used to request S3 upload permissions. For most use cases, use
     * $upload->uploadFile() or $upload->uploadFromUrl() instead.
     *
     * @var UploadRequest
     */
    protected UploadRequest $upload_request;

    /**
     * API endpoint for upload collection (folder) operations
     *
     * @var UploadCollection
     */
    protected UploadCollection $upload_collection;

    /**
     * API endpoint for upload tag operations
     *
     * @var UploadTag
     */
    protected UploadTag $upload_tag;

    /**
     * API endpoint for upload smart tag operations (read-only)
     *
     * @var UploadSmartTag
     */
    protected UploadSmartTag $upload_smart_tag;


    /**
     * API endpoint for scheduling unpublishing operations
     *
     * @var ScheduledUnpublishing
     */
    protected ScheduledUnpublishing $scheduled_unpublishing;

    /**
     * API endpoint for scheduled publication operations
     *
     * @var ScheduledPublication
     */
    protected ScheduledPublication $scheduled_publication;

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
    }


    public function __get(string $name): mixed {
        $classname = null;
        switch ($name) {
            case 'record':
                $classname = Record::class;
                break;
            case 'model':
                $classname = Model::class;
                break;
            case 'upload':
                $classname = Upload::class;
                break;
            case 'upload_request':
                $classname = UploadRequest::class;
                break;
            case 'upload_collection':
                $classname = UploadCollection::class;
                break;
            case 'upload_tag':
                $classname = UploadTag::class;
                break;
            case 'upload_smart_tag':
                $classname = UploadSmartTag::class;
                break;
            case 'scheduled_unpublishing':
                $classname = ScheduledUnpublishing::class;
                break;
            case 'scheduled_publication':
                $classname = ScheduledPublication::class;
                break;
        }

        if (!empty($classname)) {
            if (empty($this->$name)) {
                $this->$name = new $classname();
            }
            return $this->$name;
        }

        return null;
    }
}