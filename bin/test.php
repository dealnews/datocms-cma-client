<?php

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

require_once __DIR__ . '/../vendor/autoload.php';

$api_token = '855c0d2a3c87bb94c86430fd836dcf';

class testLogger implements LoggerInterface {

    public function emergency(\Stringable|string $message, array $context = []): void {
        $this->writeToOutput(\Psr\Log\LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(\Stringable|string $message, array $context = []): void {
        $this->writeToOutput(\Psr\Log\LogLevel::ALERT, $message, $context);
    }

    public function critical(\Stringable|string $message, array $context = []): void {
        $this->writeToOutput(\Psr\Log\LogLevel::CRITICAL, $message, $context);
    }

    public function error(\Stringable|string $message, array $context = []): void {
        $this->writeToOutput(\Psr\Log\LogLevel::ERROR, $message, $context);
    }

    public function warning(\Stringable|string $message, array $context = []): void {
        $this->writeToOutput(\Psr\Log\LogLevel::WARNING, $message, $context);
    }

    public function notice(\Stringable|string $message, array $context = []): void {
        $this->writeToOutput(\Psr\Log\LogLevel::NOTICE, $message, $context);
    }

    public function info(\Stringable|string $message, array $context = []): void {
        $this->writeToOutput(\Psr\Log\LogLevel::INFO, $message, $context);
    }

    public function debug(\Stringable|string $message, array $context = []): void {
        $this->writeToOutput(\Psr\Log\LogLevel::DEBUG, $message, $context);
    }

    public function log($level, \Stringable|string $message, array $context = []): void {
        $this->writeToOutput($level, $message, $context);
    }

    protected function writeToOutput(string $level, \Stringable|string $message, array $context = []) {
        echo '[' . $level . '] ' . $message . "\n";
    }
}

$l = new testLogger();

$client = new \DealNews\DatoCMS\CMA\Client(
    $api_token,
    null,
    $l,
    LogLevel::DEBUG
);

$params = new \DealNews\DatoCMS\CMA\Parameters\Record();
$params->filter->ids[] = '91407104';

var_export($client->record->list($params));
echo "\n";