<?php

namespace DealNews\DatoCMS\CMA\Parameters\Parts;

use Moonspot\ValueObjects\ValueObject;

class OrderBy extends ValueObject {

    protected array $order_by = [];

    public function addOrderByField(string $name, string $direction): OrderBy {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException('OrderBy direction must be ASC or DESC');
        }

        $this->order_by[$name] = $direction;

        return $this;
    }

    public function toArray(?array $data = null): array {
        $order_by = $data ?? $this->order_by;
        $order_by_set = [];
        foreach ($order_by as $field_name => $direction) {
            $order_by_set[] = $field_name . '_' . strtoupper($direction);
        }
        return $order_by_set;
    }
}