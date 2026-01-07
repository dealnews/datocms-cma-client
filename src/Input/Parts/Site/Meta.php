<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Site;

use Moonspot\ValueObjects\ValueObject;

/**
 * Defines meta data for the site
 *
 * This class is only needed when you want to update this information as part of an "update site" API request.
 * You will need to initialize the class, set your properties, and then set the resulting object on the Site::meta property.
 *
 * Usage:
 *  ```php
 *  $site_input = new Site();
 *
 *  $meta = new Meta();
 *  $meta->improved_timezone_management = true;
 *
 *  $site_input->meta = $meta;
 *  ```
 */
class Meta extends ValueObject {

    /**
     * Whether the Improved API Timezone Management opt-in product update is active or not
     *
     * @see https://www.datocms.com/product-updates/improved-timezone-management
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $improved_timezone_management = null;

    /**
     * Whether the Improved API Hex Management opt-in product update is active or not
     *
     * @see https://www.datocms.com/product-updates/improved-hex-management
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $improved_hex_management = null;

    /**
     * Whether the Improved GraphQL multi-locale fields opt-in product update is active or not
     *
     * @see https://www.datocms.com/product-updates/improved-gql-multilocale-fields
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $improved_gql_multilocale_fields = null;

    /**
     * Whether the Improved GraphQL visibility control opt-in product update is active or not
     *
     * @see https://www.datocms.com/product-updates/improved-gql-visibility-control
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $improved_gql_visibility_control = null;

    /**
     * Whether the Improved boolean fields opt-in product update is active or not
     *
     * @see https://www.datocms.com/product-updates/improved-boolean-fields
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $improved_boolean_fields = null;

    /**
     * The default value for the draft mode option in all the environment's models
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $draft_mode_default = null;

    /**
     * Whether the Improved validation at publishing opt-in product update is active or not
     *
     * @see https://www.datocms.com/product-updates/force-validations-on-records-when-publishing
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $improved_validation_at_publishing = null;

    /**
     * Whether the site has custom upload storage settings
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $custom_upload_storage_settings = null;

    /**
     * Whether the Improved exposure of inline blocks in the Content Delivery API opt-in product update is active or not
     *
     * @see https://www.datocms.com/product-updates/improved-exposure-of-inline-blocks-in-cda
     *
     * Optional: Setting to null will exclude this from the request input
     *
     * @var bool|null
     */
    public bool|null $improved_exposure_of_inline_blocks_in_cda = null;

    /**
     * Converts to API array format
     *
     * Will exclude any property from output if set to null
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Site Meta Data for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        foreach ($array as $key => $value) {
            if ($value === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }

}