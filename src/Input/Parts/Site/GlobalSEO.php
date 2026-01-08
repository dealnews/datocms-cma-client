<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Site;

use Moonspot\ValueObjects\ValueObject;

/**
 * Defines Global SEO data for the site
 *
 * This class is only needed when you want to update this information as part of an "update site" API request.
 * You will need to initialize the class, set your properties, and then set the resulting object on the Attributes::global_seo property.
 *
 * Usage:
 *  ```php
 *  $attributes = new Attributes();
 *
 *  $global_seo = new GlobalSEO();
 *  $global_seo->site_name = 'Site Name';
 *
 *  $attributes->global_seo = $global_seo;
 *  ```
 */
class GlobalSEO extends ValueObject {

    /**
     * Site name, used in social sharing
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var string|null
     */
    public string|null $site_name = null;

    /**
     * If there is no available SEO-related data for a record, this will be used as the default
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var FallbackSEO|null
     */
    public FallbackSEO|null $fallback_seo = null;

    /**
     * Title meta tag suffix
     *
     * The SEO title will include both the title of the record and this suffix if they are 60 characters or less in total
     *
     * Optional: Setting to false will exclude this from the request input
     *
     * @var string|null|false
     */
    public string|null|false $title_suffix = false;

    /**
     * URL of facebook page
     *
     * Optional: Setting to false will exclude this from the request input
     *
     * @var string|null|false
     */
    public string|null|false $facebook_page_url = false;

    /**
     * Twitter account associated to website
     *
     * Example: "@awesomewebsite"
     *
     * Optional: Setting to false will exclude this from the request input
     *
     * @var string|null|false
     */
    public string|null|false $twitter_account = false;

    /**
     * Converts to API array format
     *
     * Will exclude site_name and fallback_seo from output if set to null
     * Will exclude title_suffix, facebook_page_url, and twitter_account from output if set to false
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> global SEO for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($array['site_name'] === null) {
            unset($array['site_name']);
        }
        if ($array['fallback_seo'] === null) {
            unset($array['fallback_seo']);
        }
        if ($array['title_suffix'] === false) {
            unset($array['title_suffix']);
        }
        if ($array['facebook_page_url'] === false) {
            unset($array['facebook_page_url']);
        }
        if ($array['twitter_account'] === false) {
            unset($array['twitter_account']);
        }

        return $array;
    }
}
