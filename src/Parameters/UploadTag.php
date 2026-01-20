<?php

namespace DealNews\DatoCMS\CMA\Parameters;

/**
 * Query parameters for listing DatoCMS upload tags
 *
 * Provides pagination options for the upload tag list API.
 *
 * Usage:
 * ```php
 * $params = new UploadTag();
 * $params->page->limit = 25;
 * $tags = $client->upload_tag->list($params);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload-tag
 */
class UploadTag extends Common {

    // Inherits $page from Common
    // Upload tags have minimal filtering options

}
