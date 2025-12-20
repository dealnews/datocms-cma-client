<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

class ExternalVideo extends Common {

    /**
     * @param   string          $provider           External video provider ("youtube" or "vimeo" or "facebook")
     * @param   string          $provider_uid       Unique identifier of the video within the provider
     * @param   string          $url                URL of the video
     * @param   int             $width              Video width
     * @param   int             $height             Video height
     * @param   string          $thumbnail_url      URL for the video thumb
     * @param   string          $title              Title of the video
     *
     * @return  ExternalVideo
     */
    public function setExternalVideo(string $provider, string $provider_uid, string $url, int $width, int $height, string $thumbnail_url, string $title): static {
        return $this->set([
            'provider' => $provider,
            'provider_uid' => $provider_uid,
            'url' => $url,
            'width' => $width,
            'height' => $height,
            'thumbnail_url' => $thumbnail_url,
            'title' => $title,
        ]);
    }

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
                throw new \InvalidArgumentException($key .' must be an integer');
            } elseif ($key !== 'width' && $key !== 'height' && !is_string($value[$key])) {
                throw new \InvalidArgumentException($key . ' must be a string');
            }
        }
    }
}