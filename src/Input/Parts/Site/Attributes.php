<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Site;

use Moonspot\ValueObjects\ValueObject;

/**
 * Defines attributes for the site
 *
 * This class is only needed when you want to update this information as part of an "update site" API request.
 * You will need to initialize the class, set your properties, and then set the resulting object on the Site::attributes property.
 *
 * Usage:
 *  ```php
 *  $site_input = new Site();
 *
 *  $attributes = new Attributes();
 *  $attributes->no_index = true;
 *
 *  $site_input->attributes = $attributes;
 *  ```
 */
class Attributes extends ValueObject {

    /**
     * Whether the website needs to be indexed by search engines or not
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public ?bool $no_index = null;

    /**
     * The upload id for the favicon
     *
     * Optional: Setting to false will exclude this from the request input
     *
     * @var string|null|false
     */
    public string|null|false $favicon = false;

    /**
     * Specifies default global settings
     *
     * Optional: Setting to false will exclude this from the request input
     *
     * @var GlobalSEO|null|false
     */
    public GlobalSEO|null|false $global_seo = false;

    /**
     * Site name
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var string|null
     */
    public string|null $name = null;

    /**
     * A list of available locales
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var string[]|null
     */
    public array|null $locales = null;

    /**
     * Site default timezone
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var string|null
     */
    public string|null $timezone = null;

    /**
     * Specifies whether all users of this site need to authenticate using two-factor authentication
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $require_2fa = null;

    /**
     * Specifies whether you want IPs to be tracked in the Project usages section
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $ip_tracking_enabled = null;

    /**
     * If enabled, blocks schema changes of the primary environment
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $force_use_of_sandbox_environments = null;

    /**
     * Converts to API array format
     *
     * Will exclude no_index, name, locales, timezone, require_2fa, ip_tracking_enabled, and force_use_of_sandbox_environments from output if set to null
     * Will exclude favicon and global_seo from output if set to false
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Site Attributes for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($array['no_index'] === null) {
            unset($array['no_index']);
        }
        if ($array['favicon'] === false) {
            unset($array['favicon']);
        }
        if ($array['global_seo'] === false) {
            unset($array['global_seo']);
        }
        if ($array['name'] === null) {
            unset($array['name']);
        }
        if ($array['locales'] === null) {
            unset($array['locales']);
        }
        if ($array['timezone'] === null) {
            unset($array['timezone']);
        }
        if ($array['require_2fa'] === null) {
            unset($array['require_2fa']);
        }
        if ($array['ip_tracking_enabled'] === null) {
            unset($array['ip_tracking_enabled']);
        }
        if ($array['force_use_of_sandbox_environments'] === null) {
            unset($array['force_use_of_sandbox_environments']);
        }

        return $array;
    }
}
