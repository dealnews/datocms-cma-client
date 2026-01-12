<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Field;

use Moonspot\ValueObjects\ValueObject;

/**
 * Defines attributes for a field
 *
 * This class is only needed when you want to create/update a field.
 * You will need to initialize the class, set your properties, and then set the resulting object on the Field::attributes property.
 *
 * Usage:
 *  ```php
 *  $field = new Field();
 *
 *  $attributes = new Attributes();
 *  $attributes->label = 'Hello';
 *
 *  $field->attributes = $attributes;
 *  ```
 */
class Attributes extends ValueObject {

    /**
     * Valid values for the field_type property
     */
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

    /**
     * Optional field validations
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var array|Validators|null
     */
    public array|Validators|null $validators = null;

    /**
     * Field appearance details, plugin configuration and field add-ons
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var array|Appearance|null
     */
    public array|Appearance|null $appearance = null;

    /**
     * Ordering index
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var int|null
     */
    public ?int $position = null;

    /**
     * Field hint
     *
     * Example: "This field will be used as post title"
     *
     * Optional. Setting to false will exclude this from the request
     *
     * @var string|null|false
     */
    public string|null|false $hint = false;

    /**
     * Default value for Field.
     *
     * When the field is localized accepts an associative array of default values with site locales as keys.
     *
     * Optional. Setting to an empty array will exclude this from the request
     */
    public bool|int|float|string|null|array $default_value = [];

    /**
     * Whether deep filtering for block models is enabled in GraphQL or not
     *
     * Optional. Setting to null will exclude this from the request
     */
    public ?bool $deep_filtering_enabled = null;

    /**
     * Converts to API array format
     *
     * Returns an array with the following properties excluded if they were set to null:
     *  - label
     *  - field_type
     *  - api_key
     *  - localized
     *  - validators
     *  - appearance
     *  - position
     *  - deep_filtering_enabled
     *
     * These properties will be excluded if they are set to false:
     *  - hint
     *
     * These properties will be excluded if they are set to an empty array:
     *  - default_value
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Appearance for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($data === null) {
            if ($array['label'] === null) {
                unset($array['label']);
            }
            if ($array['field_type'] === null) {
                unset($array['field_type']);
            }
            if ($array['api_key'] === null) {
                unset($array['api_key']);
            }
            if ($array['localized'] === null) {
                unset($array['localized']);
            }
            if ($array['validators'] === null) {
                unset($array['validators']);
            }
            if ($array['appearance'] === null) {
                unset($array['appearance']);
            }
            if ($array['position'] === null) {
                unset($array['position']);
            }
            if ($array['hint'] === false) {
                unset($array['hint']);
            }
            if (empty($array['default_value']) && is_array($array['default_value'])) {
                unset($array['default_value']);
            }
            if ($array['deep_filtering_enabled'] === null) {
                unset($array['deep_filtering_enabled']);
            }
        }
        return $array;
    }
}
