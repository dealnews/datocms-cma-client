<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Relationships;

use Moonspot\ValueObjects\ValueObject;

class ItemType extends ValueObject {

    /**
     * Should always be "item_type"
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
     * The unique id of the "item_type" (model/block)
     */
    public string $id = '';


    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        return ['data' => $array];
    }
}