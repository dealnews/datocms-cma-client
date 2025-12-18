<?php

namespace DealNews\DatoCMS\CMA\HTTP;

use DealNews\DatoCMS\CMA\Exception\API;
use DealNews\DatoCMS\CMA\Exception\Decode;
use DealNews\DatoCMS\CMA\Exception\Unknown;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\MessageFormatter;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Handler {

    /**
     * How many retries will we do when the API reports an API Limit has been hit
     */
    const int MAX_RETRIES = 5;

    const string DEFAULT_BASE_URI = 'https://site-api.datocms.com';

    const string ENVIRONMENT_PLACEHOLDER = 'DEALNEWS-NULL-NN-984562910-NN-NULL-DEALNEWS';

    protected static array $instances = [];

    protected Client $client;

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
                'handler' => $handler_stack,
                'base_uri' => $base_url ?? self::DEFAULT_BASE_URI,
                'http_errors' => false,
                'headers' => [
                    'X-Api-Version' => '3',
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/vnd.api+json',
                ]
            ];

            if (!empty($environment)) {
                $config['headers']['X-Environment'] = $environment;
            }

            $this->client = new Client($config);
        }
    }


    public function execute(string $method, string $path, array $query_params = [], array $post_data = [], array $accepted_http_status = []): array {
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
        $body = json_decode($raw_body, true);
        if (!is_array($body)) {
            throw new Decode('Failed to decode JSON response', 1001, null, $raw_body);
        }
        return $body;
    }


    public static function init(string $apiToken, ?string $environment = null, ?string $base_url = null): self {
        if (empty(self::$instances[$apiToken][$environment ?? self::ENVIRONMENT_PLACEHOLDER][$base_url ?? self::DEFAULT_BASE_URI])) {
            self::$instances[$apiToken][$environment ?? self::ENVIRONMENT_PLACEHOLDER][$base_url ?? self::DEFAULT_BASE_URI] = new self($apiToken, $environment, $base_url);
        }

        return self::$instances[$apiToken][$environment ?? self::ENVIRONMENT_PLACEHOLDER][$base_url ?? self::DEFAULT_BASE_URI];
    }


    /**
     * Adds a Middleware retry method to a Guzzle Handler Stack. This retry method will automatically retry sending the last
     * API request if the server responded with an HTTP code of 429 (Too Many Requests) after a set number of seconds.
     *
     * @param   int                 $seconds            The number of seconds that should pass before we auto-retry the API request
     * @param   HandlerStack|null   $handler_stack      A Guzzle handler stack to add the retry method to (if one is not provided, a new stack will be created)
     *
     * @return  HandlerStack                            A Guzzle handler stack with the retry method in the stack
     */
    protected function autoRetry(int $seconds = 3, ?HandlerStack $handler_stack = null) : HandlerStack {
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
     * A method that decides whether we should retry the last API request or not.
     *
     * @param   int                     $retries        The number of retries that have been performed already
     * @param   Request                 $request        The API request
     * @param   Response|null           $response       The server's response
     * @param   RequestException|null   $exception      Any exception that has occurred
     *
     * @return  bool
     */
    public static function autoRetryDecider(int $retries, Request $request, ?Response $response = null, ?RequestException $exception = null) : bool {
        if (
            $retries < self::MAX_RETRIES &&
            !empty($response) &&
            $response->getStatusCode() === 429
        ) {
            return true;
        }

        return false;
    }


    protected function httpLogger(LoggerInterface $logger, string $log_level, ?HandlerStack $handler_stack = null) : HandlerStack {
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