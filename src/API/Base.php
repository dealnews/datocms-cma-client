<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Config;
use DealNews\DatoCMS\CMA\HTTP\Handler;

/**
 * Abstract base class for all API endpoint handlers
 *
 * Provides common initialization of the HTTP handler with authentication
 * and configuration from the Config singleton. Extend this class to create
 * new API endpoint handlers.
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
     * If no handler is provided, creates one using configuration from
     * the Config singleton.
     *
     * @param Handler|null $handler Optional pre-configured HTTP handler
     */
    public function __construct(?Handler $handler = null) {
        if (!empty($handler)) {
            $this->handler = $handler;
        } else {
            $apiToken = Config::init()->apiToken;
            $environment = Config::init()->environment;
            $base_url = Config::init()->base_url;
            $logger = Config::init()->logger;
            $log_level = Config::init()->log_level;

            $this->handler = new Handler(
                $apiToken,
                $environment,
                $logger,
                $log_level,
                $base_url
            );
        }
    }

}