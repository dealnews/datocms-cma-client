<?php

namespace DealNews\DatoCMS\CMA;

use DealNews\DatoCMS\CMA\API\Model;
use DealNews\DatoCMS\CMA\API\Record;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Main entry point for the DatoCMS Content Management API client
 *
 * Provides access to DatoCMS API endpoints for managing records/items. Configure
 * via constructor parameters or environment variables (DN_DATOCMS_API_TOKEN,
 * DN_DATOCMS_ENVIRONMENT, DN_DATOCMS_BASE_URL, DN_DATOCMS_LOG_LEVEL).
 *
 * Usage:
 * ```php
 * $client = new Client('your-api-token', 'your-environment');
 * $records = $client->record->list();
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
     * API endpoint for model/item-type operations
     *
     * @var Model
     */
    public readonly Model $model;

    /**
     * @param   string|null             $apiToken       API Token for access your DatoCMS project's Content Management API
     * @param   string|null             $environment    The name of the environment to connect to in DatoCMS (defaults to the main/primary environment)
     * @param   LoggerInterface|null    $logger         Optional logger to log API requests/responses
     * @param   string                  $log_level      PSR-3 LogLevel (defaults to "info")
     * @param   string|null             $base_url       Optional base url to use instead of default. (Useful for proxies)
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
        $this->model = new Model();
    }

}