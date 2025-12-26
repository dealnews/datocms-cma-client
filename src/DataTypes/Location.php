<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

/**
 * DataType for geographic location field values
 *
 * Represents coordinates with latitude (-90 to 90) and longitude (-180 to 180).
 *
 * Usage:
 * ```php
 * $location = Location::init()->setLocation(40.7128, -74.0060); // NYC
 * // or
 * $location = Location::init()->set([
 *     'latitude' => 40.7128,
 *     'longitude' => -74.0060
 * ]);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item
 */
class Location extends Common {

    /**
     * Sets the location using latitude and longitude
     *
     * @param float $latitude  Latitude (-90.0 to 90.0)
     * @param float $longitude Longitude (-180.0 to 180.0)
     *
     * @return static This instance for method chaining
     *
     * @throws \InvalidArgumentException If coordinates are out of range
     */
    public function setLocation(float $latitude, float $longitude): static {
        return $this->set([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    /**
     * Validates the location value format
     *
     * Requires an array with 'latitude' (-90 to 90) and 'longitude' (-180 to 180).
     *
     * @param mixed $value Value to validate
     *
     * @return void
     *
     * @throws \InvalidArgumentException If format is invalid or values out of range
     */
    protected function validateValue(mixed $value): void {
        if (is_null($value)) {
            return;
        }
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Value not in expected format');
        }
        foreach (['latitude', 'longitude'] as $key) {
            if (!array_key_exists($key, $value)) {
                throw new \InvalidArgumentException('Value not in expected format');
            } elseif ($key === 'latitude' && filter_var($value[$key], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => -90, 'max_range' => 90]]) === false) {
                throw new \InvalidArgumentException('Latitude not in the expected format');
            } elseif ($key === 'longitude' && filter_var($value[$key], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => -180, 'max_range' => 180]]) === false) {
                throw new \InvalidArgumentException('Longitude not in the expected format');
            }
        }
    }
}