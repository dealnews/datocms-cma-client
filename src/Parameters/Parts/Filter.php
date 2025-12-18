<?php

namespace DealNews\DatoCMS\CMA\Parameters\Parts;

use Moonspot\ValueObjects\ValueObject;

class Filter extends ValueObject {

    public array $ids = [];

    public array $type = [];

    public ?string $query = null;

    public FilterFields $fields;

    public bool $only_valid = false;

    public function __construct() {
        $this->fields = new FilterFields();
    }

    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        foreach ($array as $key => $value) {
            if (empty($value)) {
                unset($array[$key]);
            } elseif ($key === 'ids' || $key === 'type') {
                $array[$key] = implode(',', $value);
            }
        }
        return $array;
    }
}