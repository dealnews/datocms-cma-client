<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Site;

use Moonspot\ValueObjects\ValueObject;

/**
 * Defines fallback SEO data for when a record on the site does not have SEO data
 *
 * This class is only needed when you want to update this information as part of an "update site" API request.
 * You will need to initialize the class, set your properties, and then set the resulting object on the GlobalSEO::fallback_seo property.
 *
 * Usage:
 *  ```php
 *  $global_seo = new GlobalSEO();
 *
 *  $fallback_seo = new FallbackSEO();
 *  $fallback_seo->title = 'Default meta title';
 *  $fallback_seo->description = 'Default meta description';
 *  $fallback_seo->image = null;
 *  $fallback_seo->twitter_card = 'summary';
 *
 *  $global_seo->fallback_seo = $fallback_seo;
 *  ```
 */
class FallbackSEO extends ValueObject {

    /**
     * Example: "Default meta title"
     *
     * Required
     *
     * @var string
     */
    public string $title = '';

    /**
     * Example: "Default meta description"
     *
     * Required
     *
     * @var string
     */
    public string $description = '';

    /**
     * The id of the image
     *
     * Required (can be set to null)
     *
     * @var string|null
     */
    public ?string $image = null;

    /**
     * Determines how a Twitter link preview is shown
     *
     * Optional: Setting to false will exclude this from the request input
     *
     * If set to a string, it must be either "summary" or "summary_large_image"
     *
     * @var string|null|false
     */
    public string|null|false $twitter_card = false {
        set {
            if ($value !== 'summary' && $value !== 'summary_large_image' && $value !== false && $value !== null) {
                throw new \InvalidArgumentException('twitter_card must be "summary", "summary_large_image", null (or can be set to false if you don\'t want to send it with your request)');
            }
            $this->twitter_card = $value;
        }
    }

    /**
     * Converts to API array format
     *
     * Will exclude twitter_card from output if set to false
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> fallback SEO for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($array['twitter_card'] === false) {
            unset($array['twitter_card']);
        }
        return $array;
    }
}
