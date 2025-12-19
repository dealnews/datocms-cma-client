<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

class Color extends Common {

    /**
     * @param   int     $red        Integer between 0 and 255
     * @param   int     $green      Integer between 0 and 255
     * @param   int     $blue       Integer between 0 and 255
     * @param   int     $alpha      Integer between 0 and 255
     *
     * @return  Color
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