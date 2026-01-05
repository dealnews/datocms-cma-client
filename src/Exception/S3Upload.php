<?php

namespace DealNews\DatoCMS\CMA\Exception;

/**
 * Exception thrown when an S3 upload operation fails
 *
 * Thrown by the Upload API helper methods when the direct-to-S3 upload step
 * fails. This distinguishes S3 failures from DatoCMS API failures (which throw
 * the API exception).
 *
 * Usage:
 * ```php
 * try {
 *     $client->upload->uploadFile('/path/to/file.jpg');
 * } catch (S3Upload $e) {
 *     echo "S3 upload failed: " . $e->getMessage();
 *     echo "HTTP Status: " . $e->getCode();
 *     echo "Response: " . $e->getResponseBody();
 * }
 * ```
 *
 * @see \DealNews\DatoCMS\CMA\API\Upload::uploadFile()
 * @see \DealNews\DatoCMS\CMA\API\Upload::uploadFromUrl()
 */
class S3Upload extends \RuntimeException {

    /**
     * Raw response body from S3
     *
     * @var string|null
     */
    protected ?string $response_body = null;

    /**
     * Creates a new S3 upload exception
     *
     * @param string          $message       Exception message
     * @param int             $code          HTTP status code from S3
     * @param \Throwable|null $previous      Previous exception
     * @param string|null     $response_body Raw S3 response body
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
     * Returns the raw S3 response body
     *
     * @return string|null Response body or null if not available
     */
    public function getResponseBody(): ?string {
        return $this->response_body;
    }
}
