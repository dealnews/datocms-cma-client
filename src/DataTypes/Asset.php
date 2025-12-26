<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

/**
 * DataType for asset/file upload field values
 *
 * Represents a reference to an uploaded asset in DatoCMS with optional
 * metadata overrides for title, alt text, focal point, and custom data.
 *
 * Usage:
 * ```php
 * $asset = Asset::init()->setAsset('upload_123', 'Image Title', 'Alt Text');
 * // With focal point
 * $asset = Asset::init()->setAsset(
 *     'upload_123',
 *     'Title',
 *     'Alt',
 *     0.5,  // x focal point (0-1)
 *     0.5   // y focal point (0-1)
 * );
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item
 */
class Asset extends Common {

    /**
     * Sets the asset with optional metadata overrides
     *
     * @param string           $upload_id   ID of the uploaded asset (required)
     * @param string|null      $title       Override asset's default title
     * @param string|null      $alt         Override asset's default alt text
     * @param float|null       $focal_x     Focal point X coordinate (0.0-1.0)
     * @param float|null       $focal_y     Focal point Y coordinate (0.0-1.0)
     * @param array<string, mixed>|null $custom_data Custom metadata key-value pairs
     *
     * @return static This instance for method chaining
     *
     * @throws \InvalidArgumentException If focal point values are out of range
     */
    public function setAsset(
        string $upload_id,
        ?string $title = null,
        ?string $alt = null,
        ?float $focal_x = null,
        ?float $focal_y = null,
        ?array $custom_data = null
    ): static {
        $upload = [
            'upload_id' => $upload_id,
        ];
        if (!is_null($title)) {
            $upload['title'] = $title;
        }
        if (!is_null($alt)) {
            $upload['alt'] = $alt;
        }
        if (!is_null($focal_x) && !is_null($focal_y)) {
            $upload['focal_point'] = [
                'x' => $focal_x,
                'y' => $focal_y,
            ];
        }
        if (!is_null($custom_data)) {
            $upload['custom_data'] = $custom_data;
        }
        return $this->set($upload);
    }

    /**
     * Validates the asset value format
     *
     * Requires an array with 'upload_id' (string). Optional keys:
     * - 'title' (string)
     * - 'alt' (string)
     * - 'focal_point' (array with 'x' and 'y' floats, each 0.0-1.0)
     * - 'custom_data' (array)
     *
     * @param mixed $value Value to validate
     *
     * @return void
     *
     * @throws \InvalidArgumentException If format is invalid
     */
    protected function validateValue(mixed $value): void {
        if (is_null($value)) {
            return;
        }
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Value not in expected format');
        }
        foreach (['upload_id', 'title', 'alt', 'focal_point', 'custom_data'] as $key) {
            if ($key ==='upload_id' && !array_key_exists($key, $value)) {
                throw new \InvalidArgumentException('Value not in expected format');
            } elseif (array_key_exists($key, $value)) {
                if ($key === 'focal_point' && !is_null($value[$key]) && !is_array($value[$key])) {
                    throw new \InvalidArgumentException('focal_point not in expected format');
                } elseif (
                    $key === 'focal_point' &&
                    !is_null($value[$key]) &&
                    (
                        !array_key_exists('x', $value[$key]) ||
                        !array_key_exists('y', $value[$key]) ||
                        filter_var($value[$key]['x'], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0, 'max_range' => 1]]) === false ||
                        filter_var($value[$key]['y'], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0, 'max_range' => 1]]) === false
                    )
                ) {
                    throw new \InvalidArgumentException('focal_point not in expected format');
                } elseif ($key === 'custom_data' && !is_array($value[$key])) {
                    throw new \InvalidArgumentException('custom_data not in expected format');
                } elseif ($key !== 'focal_point' && $key !== 'custom_data' && !is_string($value[$key])) {
                    throw new \InvalidArgumentException("$key is not a string");
                }
            }
        }
    }
}