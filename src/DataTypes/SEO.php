<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

class SEO extends Common {

    /**
     * @param   string  $title              Title meta tag (max. 320 characters)
     * @param   string  $description        Description meta tag (max. 320 characters)
     * @param   string  $image              Asset to be used for social shares (image id)
     * @param   string  $twitter_card       Type of Twitter card to use ("summary" or "summary_large_image")
     * @param   bool    $no_index           Whether the noindex meta tag should be returned
     *
     * @return  SEO
     */
    public function setSEO(string $title, string $description, string $image, string $twitter_card, bool $no_index): static {
        return $this->set([
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'twitter_card' => $twitter_card,
            'no_index' => $no_index,
        ]);
    }

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