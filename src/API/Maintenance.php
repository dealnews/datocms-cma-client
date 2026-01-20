<?php

namespace DealNews\DatoCMS\CMA\API;

/**
 * API handler for DatoCMS for managing maintenance mode
 *
 * Provides methods for checking the current status of maintenance mode, activating maintenance mode, and deactivating maintenance mode.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $mode = $client->maintenance->retrieve();
 * $client->maintenance->activate();
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/maintenance-mode
 */
class Maintenance extends Base {

    /**
     * Retrieve maintenance mode
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/maintenance-mode/self
     *
     * @return array<string, mixed>     The current status of maintenance mode (is it active or not)
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(): array {
        return $this->handler->execute('GET', '/maintenance-mode');
    }

    /**
     * Activate maintenance mode
     *
     * WARNING: this means that the primary environment will be read-only
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/maintenance-mode/activate
     *
     * @param   bool                    $force      If true, will force the activation, even if there are collaborators editing some records.
     *
     * @return  array<string, mixed>                The current status of maintenance mode (is it active or not)
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function activate(bool $force = false): array {
        $query_params = [];
        if ($force) {
            $query_params['force'] = true;
        }

        return $this->handler->execute('PUT', '/maintenance-mode/activate', $query_params);
    }

    /**
     * De-activate maintenance mode
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/maintenance-mode/deactivate
     *
     * @return array<string, mixed>                    The current status of maintenance mode (is it active or not)
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function deactivate(): array {
        return $this->handler->execute('PUT', '/maintenance-mode/deactivate');
    }
}
