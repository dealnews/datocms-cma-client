<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Relationships;

use Moonspot\ValueObjects\ValueObject;

/**
 * Item type (model) relationship for a DatoCMS record
 *
 * Required relationship that specifies which model/item_type the record
 * belongs to. The type is always 'item_type'.
 *
 * Usage:
 * ```php
 * $record->relationships->item_type->id = 'DxMaW10UQiCmZcuuA-IkkA';
 * ```
 */
class ItemType extends ValueObject {

    /**
     * Relationship type, always "item_type"
     *
     * Enforced by setter - attempting to set any other value throws an exception.
     *
     * @var string
     */
    public string $type = 'item_type' {
        set {
            if ($value !== 'item_type') {
                throw new \InvalidArgumentException('Type must be "item_type".');
            }
            $this->type = $value;
        }
    }

    /**
     * Item type (model) ID
     *
     * The unique identifier for the DatoCMS model this record belongs to.
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
     * @return array<string, mixed> Item type relationship for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        return ['data' => $array];
    }
}