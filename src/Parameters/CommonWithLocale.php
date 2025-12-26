<?php

namespace DealNews\DatoCMS\CMA\Parameters;

/**
 * Abstract base class for API query parameters with locale support
 *
 * Extends Common with an optional locale parameter for filtering
 * results by language.
 */
abstract class CommonWithLocale extends Common {

    /**
     * Filter results by locale code
     *
     * When set, only returns content for the specified locale.
     *
     * @var string|null
     */
    public ?string $locale = null;

}