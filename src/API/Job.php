<?php

namespace DealNews\DatoCMS\CMA\API;

/**
 * API handler for DatoCMS job result operations
 *
 * Provides a method for retrieving a job result
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $job = $client->job->retrieve('job-id');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/job-result
 */
class Job extends Base {

    /**
     * Retrieve a job result
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/job-result/self
     *
     * @param   string                  $job_id     The ID of the job to retrieve
     *
     * @return  array<string, mixed>                The job result data
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(string $job_id): array {
        return $this->handler->execute('GET', '/job-results/' . $job_id);
    }

}
