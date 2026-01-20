<?php

namespace DealNews\DatoCMS\CMA\Parameters;

use DealNews\DatoCMS\CMA\Parameters\Parts\Page;
use Moonspot\ValueObjects\ValueObject;

/**
 * Abstract base class for API query parameters
 *
 * Provides common pagination support for all parameter classes.
 * Extend this class for endpoints that require pagination.
 */
abstract class Common extends ValueObject {

    /**
     * Pagination parameters (offset and limit)
     *
     * @var Page
     */
    public Page $page;


    /**
     * Initializes the page parameter object
     */
    public function __construct() {
        $this->page = new Page();
    }

    /**
     * Converts parameters to array, excluding empty page
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Query parameters for API request
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['page'])) {
            unset($array['page']);
        }

        return $array;
    }
}
