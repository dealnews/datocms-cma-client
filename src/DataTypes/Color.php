<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

/**
 * DataType for RGBA color field values
 *
 * Represents colors with red, green, blue, and alpha channels,
 * each as an integer from 0-255.
 *
 * Usage:
 * ```php
 * $color = Color::init()->setColor(255, 128, 64, 200);
 * // or
 * $color = Color::init()->set([
 *     'red' => 255,
 *     'green' => 128,
 *     'blue' => 64,
 *     'alpha' => 200
 * ]);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item
 */
class Color extends Common {

    /**
     * Sets the color using RGBA values
     *
     * @param int $red   Red channel (0-255)
     * @param int $green Green channel (0-255)
     * @param int $blue  Blue channel (0-255)
     * @param int $alpha Alpha channel (0-255, where 255 is fully opaque)
     *
     * @return static This instance for method chaining
     *
     * @throws \InvalidArgumentException If any value is out of range
     */
    public function setColor(int $red, int $green, int $blue, int $alpha): static {
        $this->set([
            'red' => $red,
            'green' => $green,
            'blue' => $blue,
            'alpha' => $alpha,
        ]);
        return $this;
    }

    /**
     * Validates the color value format
     *
     * Requires an array with 'red', 'green', 'blue', 'alpha' keys,
     * each with integer values from 0-255.
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
        foreach (['red', 'green', 'blue', 'alpha'] as $color) {
            if (!array_key_exists($color, $value) || filter_var($value[$color], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 255]]) === false) {
                throw new \InvalidArgumentException("Invalid color attribute: '{$color}'");
            }
        }
    }
}