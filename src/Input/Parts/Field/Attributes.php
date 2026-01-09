<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Field;

use Moonspot\ValueObjects\ValueObject;

class Attributes extends ValueObject {

    const array VALID_FIELD_TYPES = [
        'boolean',
        'color',
        'date',
        'date_time',
        'file',
        'float',
        'gallery',
        'integer',
        'json',
        'lat_lon',
        'link',
        'links',
        'rich_text',
        'seo',
        'single_block',
        'slug',
        'string',
        'structured_text',
        'text',
        'video',
    ];

    /**
     * The label of the field
     *
     * Required to be a string for creating fields,
     * Optional when updating fields: Setting to null will exclude this from the request
     *
     * @var string|null
     */
    public ?string $label = null;


    /**
     * Type of input
     *
     * Required to be a string for creating fields,
     * Optional when updating fields: Setting to null will exclude this from the request
     *
     * For possible values, see self::VALID_FIELD_TYPES
     *
     * @var string|null
     */
    public ?string $field_type = null {
        set {
            if ($value !== null && !in_array($value, self::VALID_FIELD_TYPES)) {
                throw new \InvalidArgumentException('field_type must be null or one of: ' . implode(', ', self::VALID_FIELD_TYPES));
            }
            $this->field_type = $value;
        }
    }

    /**
     * Field API key
     *
     * Unique identifier for the field
     *
     * Required to be a string for creating fields,
     * Optional when updating fields: Setting to null will exclude this from the request
     *
     * @var string|null
     */
    public ?string $api_key = null;

    /**
     * Whether the field needs to be multilanguage or not
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var bool|null
     */
    public ?bool $localized = null;
}
