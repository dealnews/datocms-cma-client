<?php

namespace DealNews\DatoCMS\CMA\Parameters;

use DealNews\DatoCMS\CMA\Parameters\Parts\OrderBy;
use DealNews\DatoCMS\CMA\Parameters\Parts\UploadFilter;

/**
 * Query parameters for listing DatoCMS uploads
 *
 * Provides filtering, sorting, and pagination options for the upload list API.
 *
 * Usage:
 * ```php
 * $params = new Upload();
 * $params->filter->type = 'image';
 * $params->filter->query = 'banner';
 * $params->order_by->addOrderByField('created_at', 'DESC');
 * $params->page->limit = 50;
 * $uploads = $client->upload->list($params);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload/instances
 */
class Upload extends Common {

    /**
     * Sort order specification
     *
     * @var OrderBy
     */
    public OrderBy $order_by;

    /**
     * Filter criteria
     *
     * @var UploadFilter
     */
    public UploadFilter $filter;

    /**
     * Initializes order_by and filter objects
     */
    public function __construct() {
        $this->order_by = new OrderBy();
        $this->filter = new UploadFilter();
        parent::__construct();
    }

    /**
     * Converts parameters to query string array
     *
     * Excludes empty values and formats order_by as comma-separated string.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Query parameters for API request
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        foreach ($array as $key => $value) {
            if (empty($value)) {
                unset($array[$key]);
            } elseif ($key === 'order_by') {
                $array['order_by'] = implode(',', $value);
            }
        }
        return $array;
    }
}
