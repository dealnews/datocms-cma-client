<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

/**
 * DataType for external video embed field values
 *
 * Represents embedded videos from YouTube, Vimeo, or Facebook with
 * metadata including dimensions and thumbnail.
 *
 * Usage:
 * ```php
 * $video = ExternalVideo::init()->setExternalVideo(
 *     'youtube',
 *     'dQw4w9WgXcQ',
 *     'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
 *     1920,
 *     1080,
 *     'https://img.youtube.com/vi/dQw4w9WgXcQ/0.jpg',
 *     'Video Title'
 * );
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item
 */
class ExternalVideo extends Common {

    /**
     * Sets all external video fields
     *
     * @param string $provider      Video provider: 'youtube', 'vimeo', or 'facebook'
     * @param string $provider_uid  Unique video identifier on the provider
     * @param string $url           Full URL to the video
     * @param int    $width         Video width in pixels
     * @param int    $height        Video height in pixels
     * @param string $thumbnail_url URL to video thumbnail image
     * @param string $title         Video title
     *
     * @return static This instance for method chaining
     *
     * @throws \InvalidArgumentException If provider is invalid
     */
    public function setExternalVideo(
        string $provider,
        string $provider_uid,
        string $url,
        int $width,
        int $height,
        string $thumbnail_url,
        string $title
    ): static {
        return $this->set([
            'provider'      => $provider,
            'provider_uid'  => $provider_uid,
            'url'           => $url,
            'width'         => $width,
            'height'        => $height,
            'thumbnail_url' => $thumbnail_url,
            'title'         => $title,
        ]);
    }

    /**
     * Validates the external video value format
     *
     * Requires an array with all keys: 'provider' ('youtube'/'vimeo'/'facebook'),
     * 'provider_uid', 'url', 'width' (int), 'height' (int), 'thumbnail_url', 'title'.
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
        foreach (['provider', 'provider_uid', 'url', 'width', 'height', 'thumbnail_url', 'title'] as $key) {
            if (!array_key_exists($key, $value)) {
                throw new \InvalidArgumentException('Value not in expected format');
            }
            if ($key === 'provider' && $value[$key] !== 'youtube' && $value[$key] !== 'vimeo' && $value[$key] !== 'facebook') {
                throw new \InvalidArgumentException('provider must be "youtube" or "vimeo" or "facebook"');
            } elseif (($key === 'width' || $key === 'height') && filter_var($value[$key], FILTER_VALIDATE_INT) === false) {
                throw new \InvalidArgumentException($key . ' must be an integer');
            } elseif ($key !== 'width' && $key !== 'height' && !is_string($value[$key])) {
                throw new \InvalidArgumentException($key . ' must be a string');
            }
        }
    }
}
