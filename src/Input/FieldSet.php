<?php

namespace DealNews\DatoCMS\CMA\Input;

use DealNews\DatoCMS\CMA\Input\Parts\FieldSet\Attributes;
use Moonspot\ValueObjects\ValueObject;

/**
 * Input object for creating/updating DatoCMS fieldsets
 *
 * Represents the data structure for fieldset operations.
 *
 * Usage:
 * ```php
 * $fieldset = new FieldSet();
 *
 * $attributes = new Attributes();
 * $attributes->title = 'Hello';
 *
 * $fieldset->attributes = $attributes;
 * $result = $client->fieldset->update('fieldset-id', $fieldset);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/fieldset/create#body-parameters
 */
class FieldSet extends ValueObject {

    /**
     * RFC 4122 UUID of fieldset expressed in URL-safe base64 format
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var ?string
     */
    public ?string $id = null;

    /**
     * FieldSet type, must always be "fieldset"
     *
     * WARNING: This property MUST be set to "fieldset". Setting any other value
     * will cause API errors. Do not modify this property.
     *
     * @var string
     */
    public readonly string $type;

    /**
     * FieldSet attributes for creating/updating fieldsets
     *
     * Can provide an associative array or use the Attributes class.
     *
     * Optional.
     *
     * @var array<string, mixed>|Attributes
     */
    public array|Attributes $attributes = [];

    public function __construct() {
        $this->type = 'fieldset';
    }

    /**
     * Converts the fieldset to an array for API submission
     *
     * Excludes empty id and attributes.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> API-ready array structure
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
        }
        return $array;
    }
}
