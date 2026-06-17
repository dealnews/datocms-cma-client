<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Config;
use DealNews\DatoCMS\CMA\HTTP\Handler;

/**
 * Abstract base class for all API endpoint handlers
 *
 * Provides common initialization of the HTTP handler with authentication
 * and configuration from a per-instance Config object. Extend this class
 * to create new API endpoint handlers.
 *
 * @see \DealNews\DatoCMS\CMA\API\Record for an implementation example
 */
abstract class Base {

    /**
     * HTTP handler for executing API requests
     *
     * @var Handler
     */
    protected Handler $handler;

    /**
     * Initializes the API handler with an HTTP handler
     *
     * Either a handler or a config instance must be provided.
     *
     * @param Handler|null  $handler Optional pre-configured HTTP handler
     * @param Config|null   $config  Optional pre-configured Config instance
     *
     * @throws \RuntimeException If neither a handler nor a config instance is provided
     */
    public function __construct(?Handler $handler = null, ?Config $config = null) {
        if (!empty($handler)) {
            $this->handler = $handler;
        } elseif (!empty($config)) {
            $apiToken    = $config->apiToken;
            $environment = $config->environment;
            $base_url    = $config->base_url;
            $logger      = $config->logger;
            $log_level   = $config->log_level;

            $this->handler = Handler::init(
                $apiToken,
                $environment,
                $logger,
                $log_level,
                $base_url
            );
        } else {
            throw new \RuntimeException('Either a Handler or a Config instance must be provided.');
        }
    }

}
