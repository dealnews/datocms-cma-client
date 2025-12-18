<?php

namespace DealNews\DatoCMS\CMA\Parameters;

use DealNews\DatoCMS\CMA\Parameters\Parts\OrderBy;
use DealNews\DatoCMS\CMA\Parameters\Parts\Filter;

class Record extends CommonWithLocale {

    public bool $nested = false;

    public string $version = 'published' {
        set {
            if (!in_array($value, ['published', 'current'])) {
                throw new \InvalidArgumentException('version must be "published" or "current"');
            }
            $this->version = $value;
        }
    }

    public OrderBy $order_by;

    public Filter $filter;

    public function __construct() {
        $this->order_by = new OrderBy();
        $this->filter = new Filter();
        parent::__construct();
    }

    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        foreach ($array as $key => $value) {
            if (empty($value)) {
                unset($array[$key]);
            } elseif ($key === 'order_by') {
                $array['order_by'] = implode(',', $value);
            }
        }
        return $array;
    }
}