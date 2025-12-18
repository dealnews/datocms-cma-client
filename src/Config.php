<?php

namespace DealNews\DatoCMS\CMA;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Config {

    protected static Config $instance;

    protected ?string $apiToken = null;

    protected ?string $environment = null;

    protected ?string $base_url = null;

    protected ?LoggerInterface $logger = null;

    protected string $log_level = LogLevel::INFO;

    protected function __construct() {
        $this->apiToken = $this->getEnvVariable('DN_DATOCMS_API_TOKEN');
        $this->environment = $this->getEnvVariable('DN_DATOCMS_ENVIRONMENT');
        $this->base_url = $this->getEnvVariable('DN_DATOCMS_BASE_URL');
        $log_level = $this->getEnvVariable('DN_DATOCMS_LOG_LEVEL');
        if (!empty($log_level)) {
            $this->log_level = $log_level;
        }
    }


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


    public static function init(): self {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function getEnvVariable(string $variable): string|null {
        $value = getenv($variable);
        if (is_string($value)) {
            return $value;
        }
        return null;
    }
}