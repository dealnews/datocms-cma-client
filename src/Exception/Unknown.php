<?php

namespace DealNews\DatoCMS\CMA\Exception;

/**
 * Exception thrown for unexpected/unknown errors
 *
 * Wraps unexpected exceptions that occur during API operations.
 * The original exception is available via getPrevious().
 *
 * Usage:
 * ```php
 * try {
 *     $client->record->list();
 * } catch (Unknown $e) {
 *     $original = $e->getPrevious();
 *     error_log("Unexpected error: " . $original->getMessage());
 * }
 * ```
 */
class Unknown extends \RuntimeException {

}