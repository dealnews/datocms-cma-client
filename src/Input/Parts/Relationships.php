<?php

namespace DealNews\DatoCMS\CMA\Input\Parts;

use DealNews\DatoCMS\CMA\Input\Parts\Relationships\Creator;
use Moonspot\ValueObjects\ValueObject;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships\ItemType;

class Relationships extends ValueObject {

    public ItemType $item_type;

    public Creator $creator;

    public function __construct() {
        $this->item_type = new ItemType();
        $this->creator = new Creator();
    }

    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['creator'])) {
            unset($array['creator']);
        }
        return $array;
    }
}