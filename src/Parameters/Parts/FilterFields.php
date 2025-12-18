<?php

namespace DealNews\DatoCMS\CMA\Parameters\Parts;

use Moonspot\ValueObjects\ValueObject;

class FilterFields extends ValueObject {

    protected array $fields = [];

    public function addField(string $field_name, mixed $value, string $operator='eq'): FilterFields {
        $this->fields[$field_name][$operator] = $value;
        return $this;
    }

    public function toArray(?array $data = null): array {
        return $data ?? $this->fields;
    }
}