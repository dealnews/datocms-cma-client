<?php

namespace DealNews\DatoCMS\CMA\Input;

use Moonspot\ValueObjects\ValueObject;

class Field extends ValueObject {

    /**
     * RFC 4122 UUID of field expressed in URL-safe base64 format
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Field type, always "field"
     *
     * Enforced by setter - attempting to set any other value throws an exception.
     *
     * @var string
     */
    public string $type = 'field' {
        set {
            if ($value !== 'field') {
                throw new \InvalidArgumentException('Type must be "field"');
            }
            $this->type = $value;
        }
    }


    public array $attributes = [];

    public array $relationships = [];
}
