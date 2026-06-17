<?php

namespace DealNews\DatoCMS\CMA;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Per-instance configuration for the DatoCMS API client
 *
 * Reads configuration from environment variables on construction:
 * - DN_DATOCMS_API_TOKEN: API token for authentication
 * - DN_DATOCMS_ENVIRONMENT: DatoCMS environment name
 * - DN_DATOCMS_BASE_URL: Custom base URL (for proxies)
 * - DN_DATOCMS_LOG_LEVEL: PSR-3 log level
 *
 * Constructor arguments passed via Client override the environment variables.
 * Each Client instance holds its own Config, so multiple clients with different
 * tokens can coexist without interfering with one another.
 *
 * Properties can also be set directly via magic setter after construction.
 *
 * Usage:
 * ```php
 * $config = new Config();
 * $config->apiToken = 'your-token';
 * echo $config->apiToken;
 * ```
 *
 * @property    string|null             $apiToken           DatoCMS API token for authentication
 * @property    string|null             $environment        DatoCMS environment name (defaults to main/primary environment)
 * @property    string|null             $base_url           Custom base URL for API requests (useful for proxies)
 * @property    LoggerInterface|null    $logger             PSR-3 compatible logger instance
 * @property    string|null             $log_level          PSR-3 log level for API request/response logging
 */
class Config {

    /**
     * DatoCMS API token for authentication
     *
     * @var string|null
     */
    protected ?string $apiToken = null;

    /**
     * DatoCMS environment name (defaults to main/primary environment)
     *
     * @var string|null
     */
    protected ?string $environment = null;

    /**
     * Custom base URL for API requests (useful for proxies)
     *
     * @var string|null
     */
    protected ?string $base_url = null;

    /**
     * PSR-3 compatible logger instance
     *
     * @var LoggerInterface|null
     */
    protected ?LoggerInterface $logger = null;

    /**
     * PSR-3 log level for API request/response logging
     *
     * @var string
     */
    protected string $log_level = LogLevel::INFO;

    /**
     * Initializes configuration from environment variables
     */
    public function __construct() {
        $this->apiToken    = $this->getEnvVariable('DN_DATOCMS_API_TOKEN');
        $this->environment = $this->getEnvVariable('DN_DATOCMS_ENVIRONMENT');
        $this->base_url    = $this->getEnvVariable('DN_DATOCMS_BASE_URL');
        $log_level         = $this->getEnvVariable('DN_DATOCMS_LOG_LEVEL');
        if (!empty($log_level)) {
            $this->log_level = $log_level;
        }
    }

    /**
     * Magic setter for configuration properties
     *
     * Allows setting: apiToken, environment, base_url, log_level, logger
     *
     * @param string $name  Property name
     * @param mixed  $value Property value
     *
     * @return void
     */
    public function __set(string $name, $value): void {
        switch ($name) {
            case 'apiToken':
            case 'environment':
            case 'base_url':
            case 'log_level':
            case 'logger':
                $this->$name = $value;
                break;
        }
    }


    /**
     * Magic getter for configuration properties
     *
     * Allows getting: apiToken, environment, base_url, log_level, logger
     *
     * @param string $name Property name
     *
     * @return string|null|LoggerInterface Property value or null if not found
     */
    public function __get(string $name): string|null|LoggerInterface {
        switch ($name) {
            case 'apiToken':
            case 'environment':
            case 'base_url':
            case 'log_level':
            case 'logger':
                return $this->$name;
                break;
        }

        return null;
    }

    /**
     * Retrieves an environment variable value
     *
     * @param string $variable Environment variable name
     *
     * @return string|null The value if set and is a string, null otherwise
     */
    protected function getEnvVariable(string $variable): string|null {
        $value = getenv($variable);
        if (is_string($value)) {
            return $value;
        }

        return null;
    }
}
