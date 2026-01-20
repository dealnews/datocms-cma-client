<?php

namespace DealNews\DatoCMS\CMA\HTTP;

use DealNews\DatoCMS\CMA\Exception\API;
use DealNews\DatoCMS\CMA\Exception\Decode;
use DealNews\DatoCMS\CMA\Exception\Unknown;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * HTTP request handler for DatoCMS API communication
 *
 * Wraps Guzzle HTTP client with automatic retry on rate limits (HTTP 429),
 * request/response logging, and JSON encoding/decoding. Handles authentication
 * headers and environment configuration.
 *
 * Features:
 * - Automatic retry on HTTP 429 with configurable delay
 * - PSR-3 compatible logging
 * - JSON-API compliant headers
 *
 * @see https://www.datocms.com/docs/content-management-api
 */
class Handler {

    /**
     * Maximum number of retry attempts for rate-limited requests
     *
     * @var int
     */
    const MAX_RETRIES = 5;

    /**
     * Default DatoCMS API base URL
     *
     * @var string
     */
    const DEFAULT_BASE_URI = 'https://site-api.datocms.com';

    /**
     * Placeholder value for null environment in instance cache key
     *
     * @var string
     */
    const ENVIRONMENT_PLACEHOLDER = 'DEALNEWS-NULL-NN-984562910-NN-NULL-DEALNEWS';

    /**
     * Cache of Handler instances keyed by sha256 of passed parameters to self::init()
     *
     * @var array<string, self>
     */
    protected static array $instances = [];

    /**
     * Guzzle HTTP client instance
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Creates a new HTTP handler
     *
     * @param string               $apiToken    DatoCMS API token
     * @param string|null          $environment DatoCMS environment name
     * @param LoggerInterface|null $logger      PSR-3 logger for request logging
     * @param string               $log_level   PSR-3 log level (default: info)
     * @param string|null          $base_url    Custom base URL for proxies
     * @param Client|null          $client      Pre-configured Guzzle client
     */
    public function __construct(
        string $apiToken,
        ?string $environment = null,
        ?LoggerInterface $logger = null,
        string $log_level = LogLevel::INFO,
        ?string $base_url = null,
        ?Client $client = null
    ) {
        if (!empty($client)) {
            $this->client = $client;
        } else {
            $handler_stack = $this->autoRetry();
            if (!empty($logger)) {
                $handler_stack = $this->httpLogger($logger, $log_level, $handler_stack);
            }

            $config = [
                'handler'     => $handler_stack,
                'base_uri'    => $base_url ?? self::DEFAULT_BASE_URI,
                'http_errors' => false,
                'headers'     => [
                    'X-Api-Version' => '3',
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/vnd.api+json',
                ],
            ];

            if (!empty($environment)) {
                $config['headers']['X-Environment'] = $environment;
            }

            $this->client = new Client($config);
        }
    }


    /**
     * Executes an HTTP request to the DatoCMS API
     *
     * @param string               $method              HTTP method (GET, POST, PUT, DELETE)
     * @param string               $path                API endpoint path
     * @param array<string, mixed> $query_params        Query string parameters
     * @param array<string, mixed> $post_data           Request body data (JSON encoded)
     * @param array<int>           $accepted_http_status Additional HTTP status codes to accept
     *
     * @return array<string, mixed> Decoded JSON response body
     *
     * @throws API     When API returns an error status code
     * @throws Decode  When JSON response cannot be decoded
     * @throws Unknown When an unexpected error occurs
     */
    public function execute(
        string $method,
        string $path,
        array $query_params = [],
        array $post_data = [],
        array $accepted_http_status = []
    ): array {
        $request_options = [];

        if (!empty($query_params)) {
            $request_options['query'] = $query_params;
        }

        if (!empty($post_data)) {
            $request_options['json'] = $post_data;
        }

        try {
            $response = $this->client->request($method, $path, $request_options);
        } catch (\Throwable $e) {
            throw new Unknown('Encountered an unexpected error', 1000, $e);
        }

        $status_code = $response->getStatusCode();
        if (($status_code < 200 || $status_code > 299) && !in_array($status_code, $accepted_http_status)) {
            throw new API('Content Management API returned with error', $status_code, null, $response->getBody()->getContents());
        }

        $raw_body = $response->getBody()->__toString();
        $body     = json_decode($raw_body, true);
        if (!is_array($body)) {
            throw new Decode('Failed to decode JSON response', 1001, null, $raw_body);
        }

        return $body;
    }


    /**
     * Returns a cached Handler instance or creates a new one
     *
     * Instances are cached by a combination of apiToken, environment, base_url, logger (fully qualified class name), and log_level.
     *
     * @param string $apiToken DatoCMS API token
     * @param string|null $environment DatoCMS environment name
     * @param LoggerInterface|null $logger PSR-3 logger for request logging
     * @param string $log_level PSR-3 log level (default: info)
     * @param string|null $base_url Custom base URL for proxies
     *
     * @return self Cached or new Handler instance
     */
    public static function init(
        string $apiToken,
        ?string $environment = null,
        ?LoggerInterface $logger = null,
        string $log_level = LogLevel::INFO,
        ?string $base_url = null
    ): self {
        $key = $apiToken;
        $key .= '_' . ($environment ?? self::ENVIRONMENT_PLACEHOLDER);
        $key .= '_' . ($base_url ?? self::DEFAULT_BASE_URI);
        $key .= '_' . $log_level;
        $key .= '_' . (!empty($logger) ? $logger::class : '0');
        $key = hash('sha256', $key);

        if (empty(self::$instances[$key])) {
            self::$instances[$key] = new self(
                $apiToken,
                $environment,
                $logger,
                $log_level,
                $base_url
            );
        }

        return self::$instances[$key];
    }

    /**
     * Resets the instance cache (for testing purposes only)
     *
     * @return void
     */
    public static function reset(): void {
        self::$instances = [];
    }


    /**
     * Configures automatic retry middleware for rate-limited requests
     *
     * Adds Guzzle middleware that retries requests when the server responds
     * with HTTP 429 (Too Many Requests) after a configurable delay.
     *
     * @param int              $seconds       Delay between retries (default: 3)
     * @param HandlerStack|null $handler_stack Existing handler stack to modify
     *
     * @return HandlerStack Handler stack with retry middleware added
     */
    protected function autoRetry(
        int $seconds = 3,
        ?HandlerStack $handler_stack = null
    ): HandlerStack {
        if (empty($handler_stack)) {
            $handler_stack = HandlerStack::create();
        }

        $delay = function (int $retries) use ($seconds) : int {
            return 1000 * $seconds;
        };

        $handler_stack->push(Middleware::retry(__CLASS__ . '::autoRetryDecider', $delay), 'auto-retry-429');

        return $handler_stack;
    }

    /**
     * Determines whether a failed request should be retried
     *
     * Called by Guzzle retry middleware. Returns true for HTTP 429 responses
     * when retry count is below MAX_RETRIES.
     *
     * @param int                   $retries   Number of retries already attempted
     * @param Request               $request   The HTTP request
     * @param Response|null         $response  The HTTP response, if available
     * @param RequestException|null $exception Any exception that occurred
     *
     * @return bool True if request should be retried
     */
    public static function autoRetryDecider(
        int $retries,
        Request $request,
        ?Response $response = null,
        ?RequestException $exception = null
    ): bool {
        if (
            $retries < self::MAX_RETRIES &&
            !empty($response)            &&
            $response->getStatusCode() === 429
        ) {
            return true;
        }

        return false;
    }


    /**
     * Configures HTTP request/response logging middleware
     *
     * Adds Guzzle middleware for logging requests and responses. Uses SHORT
     * format for INFO/NOTICE levels, DEBUG format for all other levels.
     *
     * @param LoggerInterface   $logger        PSR-3 logger instance
     * @param string            $log_level     PSR-3 log level
     * @param HandlerStack|null $handler_stack Existing handler stack to modify
     *
     * @return HandlerStack Handler stack with logging middleware added
     */
    protected function httpLogger(
        LoggerInterface $logger,
        string $log_level,
        ?HandlerStack $handler_stack = null
    ): HandlerStack {
        if (empty($handler_stack)) {
            $handler_stack = HandlerStack::create();
        }

        switch ($log_level) {
            case LogLevel::NOTICE:
            case LogLevel::INFO:
                $format = MessageFormatter::SHORT;
                break;
            default:
                $format = MessageFormatter::DEBUG;
                break;

        }

        $handler_stack->push(Middleware::log($logger, new MessageFormatter($format)));

        return $handler_stack;
    }
}
