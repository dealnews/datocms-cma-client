<?php

namespace DealNews\DatoCMS\CMA\Parameters;

/**
 * Query parameters for listing DatoCMS upload collections
 *
 * Provides pagination options for the upload collection list API.
 *
 * Usage:
 * ```php
 * $params = new UploadCollection();
 * $params->page->limit = 25;
 * $collections = $client->upload_collection->list($params);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload-collection
 */
class UploadCollection extends Common {

    // Inherits $page from Common
    // Upload collections have minimal filtering options

}
