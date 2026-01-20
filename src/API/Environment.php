<?php

namespace DealNews\DatoCMS\CMA\API;

/**
 * API handler for DatoCMS environment operations
 *
 * Provides methods for environment operations including listing,
 * forking, promoting, renaming, and deleting environments.
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $envs = $client->environment->list();
 * $env = $client->environment->retrieve('environment-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/environment
 */
class Environment extends Base {

    /**
     * Fork an existing environment
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/environment/fork
     *
     * @param   string                  $original_environment_id    ID of the environment to fork
     * @param   string                  $new_environment_id         ID of the new environment created from the fork
     * @param   bool                    $immediate_return           Whether to return immediately or wait for the fork to complete (waiting will result in a background job process)
     * @param   bool                    $fast                       Whether to perform a fast fork. Performing a fast fork reduces processing time,
     *                                                              but it also prevents writing to the source environment during the process.
     * @param   bool                    $force                      Force the start of fast fork, even if there are collaborators editing some records
     *
     * @return  array<string, mixed>                                If immediate_return is true, returns the new forked environment.
     *                                                              Otherwise, returns information about the "job" that will be running to complete the fork.
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function fork(string $original_environment_id, string $new_environment_id, bool $immediate_return = false, bool $fast = false, bool $force = false): array {
        $payload = [
            'id'   => $new_environment_id,
            'type' => 'environment',
        ];

        $query_params = [];
        if (!empty($immediate_return)) {
            $query_params['immediate_return'] = $immediate_return;
        }
        if (!empty($fast)) {
            $query_params['fast'] = $fast;
        }
        if (!empty($force)) {
            $query_params['force'] = $force;
        }

        return $this->handler->execute('POST', '/environments/' . $original_environment_id . '/fork', $query_params, ['data' => $payload]);
    }

    /**
     * Promote an environment to primary
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/environment/promote
     *
     * @param   string                  $environment_id     ID of the environment to promote
     *
     * @return  array<string, mixed>                        The promoted environment
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function promote(string $environment_id): array {
        return $this->handler->execute('PUT', '/environments/' . $environment_id . '/promote');
    }

    /**
     * Rename an environment
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/environment/rename
     *
     * @param   string                  $original_environment_id    ID of the environment to rename
     * @param   string                  $new_environment_id         New name/ID for the environment
     *
     * @return  array<string, mixed>                                The renamed environment
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function rename(string $original_environment_id, string $new_environment_id): array {
        $payload = [
            'id'   => $new_environment_id,
            'type' => 'environment',
        ];

        return $this->handler->execute('PUT', '/environments/' . $original_environment_id . '/rename', [], ['data' => $payload]);
    }

    /**
     * List all environments
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/environment/instances
     *
     * @return  array<string, mixed>                   List of environments
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function list(): array {
        return $this->handler->execute('GET', '/environments');
    }

    /**
     * Retrieve an environment
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/environment/self
     *
     * @param   string                  $environment_id     ID of the environment to retrieve
     *
     * @return  array<string, mixed>                        The retrieved environment
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $environment_id): array {
        return $this->handler->execute('GET', '/environments/' . $environment_id);
    }

    /**
     * Delete an environment
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/environment/destroy
     *
     * @param   string                  $environment_id     ID of the environment to delete
     *
     * @return  array<string, mixed>                        Information about the "job" that will be running to complete the deletion
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function delete(string $environment_id): array {
        return $this->handler->execute('DELETE', '/environments/' . $environment_id);
    }
}
