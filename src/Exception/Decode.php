<?php

namespace DealNews\DatoCMS\CMA\Exception;

/**
 * Exception thrown when JSON response decoding fails
 *
 * Stores the raw JSON string that failed to decode for debugging.
 *
 * Usage:
 * ```php
 * try {
 *     $records = $client->record->list();
 * } catch (Decode $e) {
 *     echo "Failed to decode: " . $e->getRawJson();
 * }
 * ```
 */
class Decode extends \RuntimeException {

    /**
     * Raw JSON string that failed to decode
     *
     * @var string|null
     */
    protected ?string $raw_json = null;

    /**
     * Creates a new Decode exception
     *
     * @param string          $message  Exception message
     * @param int             $code     Exception code
     * @param \Throwable|null $previous Previous exception
     * @param string|null     $raw_json Raw JSON string that failed to decode
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
        ?string $raw_json = null
    ) {
        $this->raw_json = $raw_json;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the raw JSON string that failed to decode
     *
     * @return string|null Raw JSON or null if not available
     */
    public function getRawJson(): ?string {
        return $this->raw_json;
    }

}