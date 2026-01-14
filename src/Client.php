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
use DealNews\DatoCMS\CMA\API\Site;
use DealNews\DatoCMS\CMA\API\FieldSet;
use DealNews\DatoCMS\CMA\API\Field;
use DealNews\DatoCMS\CMA\API\Environment;
use DealNews\DatoCMS\CMA\API\RecordVersion;
use DealNews\DatoCMS\CMA\API\Webhook;
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
 * @property-read   Site                        $site                           API endpoint for site operations
 * @property-read   FieldSet                    $fieldset                       API endpoint for fieldset operations
 * @property-read   Field                       $field                          API endpoint for field operations
 * @property-read   Environment                 $environment                    API endpoint for environment operations
 * @property-read   RecordVersion               $record_version                 API endpoint for record version operations
 * @property-read   Webhook                     $webhook                        API endpoint for webhook operations
 */
class Client {

    protected const array PROPERTY_MAPPING = [
        'record' => Record::class,
        'model' => Model::class,
        'upload' => Upload::class,
        'upload_request' => UploadRequest::class,
        'upload_collection' => UploadCollection::class,
        'upload_smart_tag' => UploadSmartTag::class,
        'upload_tag' => UploadTag::class,
        'scheduled_publication' => ScheduledPublication::class,
        'scheduled_unpublishing' => ScheduledUnpublishing::class,
        'site' => Site::class,
        'fieldset' => FieldSet::class,
        'field' => Field::class,
        'environment' => Environment::class,
        'record_version' => RecordVersion::class,
        'webhook' => Webhook::class,
    ];

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
     * API endpoint for site operations
     *
     * @var Site
     */
    protected Site $site;

    /**
     * API endpoint for fieldset operations
     *
     * @var FieldSet
     */
    protected FieldSet $fieldset;

    /*
     * API endpoint for field operations
     *
     * @var Field
     */
    protected Field $field;

    /**
     * API endpoint for environment operations
     *
     * @var Environment
     */
    protected Environment $environment;

    /*
     * API endpoint for record version operations
     *
     * @var RecordVersion
     */
    protected RecordVersion $record_version;

    /**
     * API endpoint for webhook operations
     *
     * @var Webhook
     */
    protected Webhook $webhook;

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
        $value = $this->getAPIProperty($name);

        if (is_null($value)) {
            trigger_error(
                'Undefined property: ' . static::class . '::$' . $name,
                E_USER_WARNING
            );
        }

        return $value;
    }


    public function __isset(string $name): bool {
        $value = $this->getAPIProperty($name);

        return !is_null($value);
    }


    /**
     * Logic for loading property classes at the last minute.
     *
     * @param   string  $name   The name of the property to retrieve
     *
     * @return  mixed           The property value or null if not found
     */
    protected function getAPIProperty(string $name): mixed {
        $classname = null;
        if (array_key_exists($name, self::PROPERTY_MAPPING)) {
            $classname = self::PROPERTY_MAPPING[$name];
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
