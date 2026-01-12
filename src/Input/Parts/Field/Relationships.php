<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Field;

use DealNews\DatoCMS\CMA\Input\Parts\Relationships\FieldSet;
use Moonspot\ValueObjects\ValueObject;

/**
 * Represents the relationships between a field and other entities.
 */
class Relationships extends ValueObject {

    /**
     * @var FieldSet
     */
    public FieldSet $fieldset;

    public function __construct() {
        $this->fieldset = new FieldSet();
    }
}
