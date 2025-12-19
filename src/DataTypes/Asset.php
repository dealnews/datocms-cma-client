<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

class Asset extends Common {

    /**
     * @param   string          $upload_id      ID of an asset (required)
     * @param   string|null     $title          Title for the asset, if you want to override the asset's default value
     * @param   string|null     $alt            Alternate text for the asset, if you want to override the asset's default value
     * @param   float|null      $focal_x        Focal point for the asset, if you want to override the asset's default value (x coordinate, between 0 and 1)
     * @param   float|null      $focal_y        Focal point for the asset, if you want to override the asset's default value (y coordinate, between 0 and 1)
     * @param   array|null      $custom_data    An associative array containing custom keys that you can use on your frontend projects
     *
     * @return  Asset
     */
    public function setAsset(string $upload_id, ?string $title = null, ?string $alt = null, ?float $focal_x = null, ?float $focal_y = null, ?array $custom_data = null): static {
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
                    (
                        !array_key_exists('x', $value[$key]) ||
                        !array_key_exists('y', $value[$key]) ||
                        filter_var($value[$key]['x'], FILTER_VALIDATE_FLOAT, ['min_range' => 0, 'max_range' => 1]) === false ||
                        filter_var($value[$key]['y'], FILTER_VALIDATE_FLOAT, ['min_range' => 0, 'max_range' => 1]) === false
                    )
                ) {
                    throw new \InvalidArgumentException('focal_point not in expected format');
                } elseif ($key === 'custom_data' && !is_array($value[$key])) {
                    throw new \InvalidArgumentException('custom_data not in expected format');
                } elseif (!is_string($value[$key])) {
                    throw new \InvalidArgumentException("$key is not a string");
                }
            }
        }
    }
}