<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Input\Site as SiteInput;
use DealNews\DatoCMS\CMA\Parameters\Site as SiteParameters;

/**
 * API handler for DatoCMS site operations
 *
 * Provides methods for retrieving current information/settings about the site and updating those settings
 *
 * Usage:
 * ```php
 * $client = new Client($token);
 * $site_info = $client->site->retrieve();
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/site
 */
class Site extends Base {

    /**
     * Retrieve the site information
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/site/self
     *
     * @param   SiteParameters|null     $parameters     Optional parameters for the request (can request additional related data using 'include')
     *
     * @return  array<string, mixed>                    Site information
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function retrieve(?SiteParameters $parameters = null): array {
        $query_params = [];
        if (!empty($parameters) && !empty($parameters->include)) {
            $query_params['include'] = implode(',', $parameters->include);
        }

        return $this->handler->execute('GET', '/site', $query_params);
    }


    /**
     * Update settings for the site
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/site/update
     *
     * @param   array<string, mixed>|SiteInput     $data       Site input data
     *
     * @return array<string, mixed>             The updated site information
     *
     * @throws \DealNews\DatoCMS\CMA\Exception\API     On HTTP error responses
     * @throws \DealNews\DatoCMS\CMA\Exception\Decode  On JSON decode failure
     * @throws \DealNews\DatoCMS\CMA\Exception\Unknown On unexpected errors
     */
    public function update(array|SiteInput $data): array {
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return $this->handler->execute('PUT', '/site', [], ['data' => $data]);
    }
}
