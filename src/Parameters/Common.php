<?php

namespace DealNews\DatoCMS\CMA\Parameters;

use DealNews\DatoCMS\CMA\Parameters\Parts\Page;
use Moonspot\ValueObjects\ValueObject;

abstract class Common extends ValueObject {

    public Page $page;


    public function __construct() {
        $this->page = new Page();
    }

    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['page'])) {
            unset($array['page']);
        }
        return $array;
    }
}