<?php

namespace DealNews\DatoCMS\CMA\Input\Parts;

use DealNews\DatoCMS\CMA\Input\Parts\Relationships\Creator;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships\ItemType;
use Moonspot\ValueObjects\ValueObject;

/**
 * Relationship definitions for a DatoCMS record
 *
 * Defines the item_type (model) the record belongs to and optionally
 * the creator information.
 *
 * Usage:
 * ```php
 * $record->relationships->item_type->id = 'model-id';
 * $record->relationships->creator->type = 'user';
 * $record->relationships->creator->id = 'user-id';
 * ```
 */
class Relationships extends ValueObject {

    /**
     * Item type (model) relationship
     *
     * Required for creating records. Specifies which model this record belongs to.
     *
     * @var ItemType
     */
    public ItemType $item_type;

    /**
     * Creator relationship
     *
     * Optional. If not set, DatoCMS determines the creator automatically.
     * Excluded from output if type and id are not both set.
     *
     * @var Creator
     */
    public Creator $creator;

    /**
     * Initializes relationship objects
     */
    public function __construct() {
        $this->item_type = new ItemType();
        $this->creator   = new Creator();
    }

    /**
     * Converts relationships to array, excluding empty creator
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Relationships array for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['creator'])) {
            unset($array['creator']);
        }

        return $array;
    }
}
