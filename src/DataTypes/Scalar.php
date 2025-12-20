<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

class Scalar extends Common {

    protected function validateValue(mixed $value): void {
        if (is_null($value)) {
            return;
        }
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException('Value must be scalar');
        }
    }
}