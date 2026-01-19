<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Relationships;

use Moonspot\ValueObjects\ValueObject;

/**
 * FieldSet relationship for field DatoCMS objects
 *
 * Usage:
 * ```php
 * $fieldset = new FieldSet();
 * $fieldset->id = '24';
 *
 * $field->relationships->fieldset = $fieldset;
 * ```
 */
class FieldSet extends ValueObject {

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
     * FieldSet ID
     *
     * The unique identifier for the DatoCMS fieldset.
     *
     * @var string
     */
    public string $id = '';

    public function __construct() {
        $this->type = 'fieldset';
    }

    /**
     * Converts to API array format
     *
     * Wraps type and id in {data: {...}} structure as required by the API.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> FieldSet type relationship for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        return ['data' => $array];
    }
}
