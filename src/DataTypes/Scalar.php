<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

/**
 * DataType for scalar field values (string, integer, float, boolean)
 *
 * Use for simple text fields, numbers, and boolean values in DatoCMS.
 *
 * Usage:
 * ```php
 * $title = Scalar::init()->set('Hello World');
 * $count = Scalar::init()->set(42);
 * $active = Scalar::init()->set(true);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item
 */
class Scalar extends Common {

    /**
     * Validates that the value is scalar (string, int, float, bool) or null
     *
     * @param mixed $value Value to validate
     *
     * @return void
     *
     * @throws \InvalidArgumentException If value is not scalar
     */
    protected function validateValue(mixed $value): void {
        if (is_null($value)) {
            return;
        }
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException('Value must be scalar');
        }
    }
}