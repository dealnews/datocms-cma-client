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
     * FieldSet type, can only be set to "fieldset"
     *
     * @var string
     */
    public string $type = 'fieldset' {
        set {
            if ($value !== 'fieldset') {
                throw new \InvalidArgumentException('Type must be "fieldset"');
            }
            $this->type = $value;
        }
    }

    /**
     * FieldSet ID
     *
     * The unique identifier for the DatoCMS fieldset.
     *
     * @var string
     */
    public string $id = '';

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
