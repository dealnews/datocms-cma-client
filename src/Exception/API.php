<?php

namespace DealNews\DatoCMS\CMA\Exception;

/**
 * Exception thrown when the DatoCMS API returns an error response
 *
 * Stores the HTTP status code in the exception code and provides access
 * to the raw response body for debugging.
 *
 * Usage:
 * ```php
 * try {
 *     $client->record->retrieve('invalid-id');
 * } catch (API $e) {
 *     echo "HTTP " . $e->getCode() . ": " . $e->getResponseBody();
 * }
 * ```
 */
class API extends \RuntimeException {

    /**
     * Raw response body from the API
     *
     * @var string|null
     */
    protected ?string $response_body = null;

    /**
     * Creates a new API exception
     *
     * @param string          $message       Exception message
     * @param int             $code          HTTP status code
     * @param \Throwable|null $previous      Previous exception
     * @param string|null     $response_body Raw API response body
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
        ?string $response_body = null
    ) {
        $this->response_body = $response_body;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the raw API response body
     *
     * @return string|null Response body or null if not available
     */
    public function getResponseBody(): ?string {
        return $this->response_body;
    }
}
