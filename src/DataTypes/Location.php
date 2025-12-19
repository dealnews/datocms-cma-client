<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

class Location extends Common {

    /**
     * @param   float       $latitude       Float between -90.0 to 90
     * @param   float       $longitude      Float between -180.0 to 180
     *
     * @return  Location
     */
    public function setLocation(float $latitude, float $longitude) : static {
        return $this->set([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

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