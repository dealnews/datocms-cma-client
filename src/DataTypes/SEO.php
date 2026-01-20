<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

/**
 * DataType for SEO metadata field values
 *
 * Represents SEO-related metadata including title, description, social image,
 * Twitter card type, and noindex setting.
 *
 * Usage:
 * ```php
 * $seo = SEO::init()->setSEO(
 *     'Page Title',
 *     'Page description for search engines',
 *     'image_upload_id',
 *     'summary_large_image',
 *     false  // noindex
 * );
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item
 */
class SEO extends Common {

    /**
     * Sets all SEO metadata fields
     *
     * @param string $title        Title meta tag (max 320 characters)
     * @param string $description  Description meta tag (max 320 characters)
     * @param string $image        Upload ID for social share image
     * @param string $twitter_card Twitter card type: 'summary' or 'summary_large_image'
     * @param bool   $no_index     Whether to include noindex meta tag
     *
     * @return static This instance for method chaining
     *
     * @throws \InvalidArgumentException If twitter_card value is invalid
     */
    public function setSEO(
        string $title,
        string $description,
        string $image,
        string $twitter_card,
        bool $no_index
    ): static {
        return $this->set([
            'title'        => $title,
            'description'  => $description,
            'image'        => $image,
            'twitter_card' => $twitter_card,
            'no_index'     => $no_index,
        ]);
    }

    /**
     * Validates the SEO value format
     *
     * Requires an array with all keys: 'title', 'description', 'image',
     * 'twitter_card' ('summary' or 'summary_large_image'), and 'no_index' (bool).
     *
     * @param mixed $value Value to validate
     *
     * @return void
     *
     * @throws \InvalidArgumentException If format is invalid or values are wrong type
     */
    protected function validateValue(mixed $value): void {
        if (is_null($value)) {
            return;
        }
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Value not in expected format');
        }
        foreach (['title', 'description', 'image', 'twitter_card', 'no_index'] as $key) {
            if (!array_key_exists($key, $value)) {
                throw new \InvalidArgumentException('Value not in expected format');
            }
            if ($key === 'twitter_card' && $value[$key] !== 'summary' && $value[$key] !== 'summary_large_image') {
                throw new \InvalidArgumentException('twitter_card must be "summary" or "summary_large_image"');
            }
            if ($key === 'no_index' && $value[$key] !== true && $value[$key] !== false) {
                throw new \InvalidArgumentException('no_index must be boolean');
            }
        }
    }
}
