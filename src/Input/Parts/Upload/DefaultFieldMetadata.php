<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Upload;

use Moonspot\ValueObjects\ValueObject;
use stdClass;

/**
 * Localized default field metadata for DatoCMS uploads
 *
 * Stores alt text, title, focal point, and custom data per locale. When an
 * upload is used in a record, these values serve as defaults for that locale.
 *
 * Usage:
 * ```php
 * $metadata = new DefaultFieldMetadata();
 * $metadata->addLocale('en', 'English alt text', 'English title');
 * $metadata->addLocale('es', 'Texto alternativo', 'Título en español');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload/create
 */
class DefaultFieldMetadata extends ValueObject {

    /**
     * Localized metadata entries keyed by locale code
     *
     * Structure: [locale => [alt, title, focal_point, custom_data]]
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $locales = [];

    /**
     * Adds or updates metadata for a specific locale
     *
     * @param string                    $locale      Locale code (e.g., 'en', 'es')
     * @param string|null               $alt         Alt text for accessibility
     * @param string|null               $title       Title text
     * @param array<string, float>|null $focal_point Focal point coordinates
     *                                               ['x' => 0.0-1.0, 'y' => 0.0-1.0]
     * @param array<string, mixed>|null $custom_data Custom key-value data
     *
     * @return static This instance for method chaining
     *
     * @throws \InvalidArgumentException If focal_point format is invalid
     */
    public function addLocale(
        string $locale,
        ?string $alt = null,
        ?string $title = null,
        ?array $focal_point = null,
        ?array $custom_data = null
    ): static {
        if (!is_null($focal_point)) {
            $this->validateFocalPoint($focal_point);
        }

        $this->locales[$locale] = [
            'alt'         => $alt,
            'title'       => $title,
            'focal_point' => $focal_point,
            'custom_data' => $custom_data,
        ];

        return $this;
    }

    /**
     * Gets metadata for a specific locale
     *
     * @param string $locale Locale code
     *
     * @return array<string, mixed>|null Metadata array or null if not set
     */
    public function getLocale(string $locale): ?array {
        return $this->locales[$locale] ?? null;
    }

    /**
     * Checks if any locales have been set
     *
     * @return bool True if at least one locale is set
     */
    public function hasLocales(): bool {
        return !empty($this->locales);
    }

    /**
     * Gets all locale codes that have been set
     *
     * @return array<string> List of locale codes
     */
    public function getLocaleCodes(): array {
        return array_keys($this->locales);
    }

    /**
     * Validates focal point coordinates
     *
     * @param array<string, mixed> $focal_point Focal point array to validate
     *
     * @return void
     *
     * @throws \InvalidArgumentException If validation fails
     */
    protected function validateFocalPoint(array $focal_point): void {
        if (!array_key_exists('x', $focal_point) || !array_key_exists('y', $focal_point)) {
            throw new \InvalidArgumentException(
                'focal_point must contain "x" and "y" keys'
            );
        }

        $x = $focal_point['x'];
        $y = $focal_point['y'];

        if (!is_numeric($x) || !is_numeric($y)) {
            throw new \InvalidArgumentException(
                'focal_point x and y values must be numeric'
            );
        }

        if ($x < 0 || $x > 1 || $y < 0 || $y > 1) {
            throw new \InvalidArgumentException(
                'focal_point x and y values must be between 0 and 1'
            );
        }
    }

    /**
     * Converts metadata to array for API submission
     *
     * Removes null values from each locale's metadata to minimize payload size.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, array<string, mixed>> Localized metadata array
     */
    public function toArray(?array $data = null): array {
        $result = [];
        foreach ($this->locales as $locale => $metadata) {
            $result[$locale] = array_filter($metadata, fn ($v) => !is_null($v));
            if(empty($metadata['custom_data'])) {
                $result[$locale]['custom_data'] = new stdClass();
            }
        }

        return $result;
    }
}
