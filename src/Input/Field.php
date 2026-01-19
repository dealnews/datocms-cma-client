<?php

namespace DealNews\DatoCMS\CMA\Input;

use DealNews\DatoCMS\CMA\Input\Parts\Field\Attributes;
use DealNews\DatoCMS\CMA\Input\Parts\Field\Relationships;
use Moonspot\ValueObjects\ValueObject;

/**
 * Input object for creating and updating DatoCMS fields
 *
 * Represents the data structure for field operations.
 *
 * Usage:
 * ```php
 * $field = new Field();
 * $field->id = 'field-id';
 * $field->attributes['label'] = 'Hello';
 * $result = $client->field->create('model-id', $field);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/field/create
 */
class Field extends ValueObject {

    /**
     * RFC 4122 UUID of field expressed in URL-safe base64 format
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Field type, must always be "field"
     *
     * WARNING: This property MUST be set to "field". Setting any other value
     * will cause API errors. Do not modify this property.
     *
     * @var string
     */
    public readonly string $type;

    /**
     * Field attributes
     *
     * Can provide an associative array or use the Attributes class.
     *
     * All attributes are optional for updating a field, a few are required for creating a field.
     *
     * @var array|Attributes
     */
    public array|Attributes $attributes = [];

    /**
     * Relationships between the field and other entities
     *
     * Optional. Setting to null will exclude this from the update request
     *
     * @var null|Relationships
     */
    public ?Relationships $relationships = null;

    public function __construct() {
        $this->type = 'field';
    }

    /**
     * Converts to API array format
     *
     * Returns an array with the follow properties excluded if they were set to null:
     *  - id
     *  - relationships
     *
     * These properties will be excluded if they are set to an empty array:
     *  - attributes
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Field for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($data === null) {
            if ($array['id'] === null) {
                unset($array['id']);
            }
            if (empty($array['attributes'])) {
                unset($array['attributes']);
            }
            if ($array['relationships'] === null) {
                unset($array['relationships']);
            }
        }
        return $array;
    }
}
