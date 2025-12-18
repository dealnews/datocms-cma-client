<?php

namespace DealNews\DatoCMS\CMA\Parameters\Parts;

use Moonspot\ValueObjects\ValueObject;

class Page extends ValueObject {

    const int DEFAULT_OFFSET = 0;

    const int DEFAULT_LIMIT = 15;

    public int $offset = self::DEFAULT_OFFSET;

    public int $limit = self::DEFAULT_LIMIT;

    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($array['offset'] === self::DEFAULT_OFFSET) {
            unset($array['offset']);
        }
        if ($array['limit'] === self::DEFAULT_LIMIT) {
            unset($array['limit']);
        }
        return $array;
    }
}