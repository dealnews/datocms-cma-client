<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Input\Plugin as PluginInput;

/**
 * API handler for DatoCMS plugin operations
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $plugin = $client->plugin->create(['package_name' => 'npm-package-name']);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/plugin
 */
class Plugin extends Base {

    /**
     * Create a new plugin
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/plugin/create
     *
     * @param   array|PluginInput   $data               The data/settings for the plugin to create
     *
     * @return  array<string, mixed>                    The created plugin
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function create(array|PluginInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute('POST', '/plugins', [], ['data' => $data]);
    }


    /**
     * Update a plugin
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/plugin/update
     *
     * @param   string              $plugin_id          ID of the plugin to update
     * @param   array|PluginInput   $data               The data/settings to update on the plugin
     *
     * @return  array<string, mixed>                    The updated plugin
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function update(string $plugin_id, array|PluginInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute('PUT', '/plugins/' . $plugin_id, [], ['data' => $data]);
    }

    /**
     * List all installed plugins
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/plugin/instances
     *
     * @return  array<string, mixed>                    List of installed plugins
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(): array {
        return $this->handler->execute('GET', '/plugins');
    }

    /**
     * Retrieve a plugin
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/plugin/self
     *
     * @param   string              $plugin_id          ID of the plugin to retrieve
     *
     * @return  array<string, mixed>                    The retrieved plugin
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $plugin_id): array {
        return $this->handler->execute('GET', '/plugins/' . $plugin_id);
    }

    /**
     * Uninstall a plugin
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/plugin/destroy
     *
     * @param   string              $plugin_id          ID of the plugin to uninstall
     *
     * @return  array<string, mixed>                    The deleted plugin
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $plugin_id): array {
        return $this->handler->execute('DELETE', '/plugins/' . $plugin_id);
    }
}
