<?php

namespace DealNews\DatoCMS\CMA\Input;

use DealNews\DatoCMS\CMA\Input\Parts\Field\Attributes;
use DealNews\DatoCMS\CMA\Input\Parts\Field\Relationships;
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

    /**
     * Field attributes
     *
     * Can provide an associative array or use the Attributes class.
     *
     * All attributes are optional for updating a field, a few are required for creating a field.
     *
     * @var array|Attributes
     */
    public array|Attributes $attributes = [];

    /**
     * Relationships between the field and other entities
     *
     * Optional. Setting to null will exclude this from the update request
     *
     * @var null|Relationships
     */
    public ?Relationships $relationships = null;

    /**
     * Converts to API array format
     *
     * Returns an array with the follow properties excluded if they were set to null:
     *  - id
     *  - relationships
     *
     * These properties will be excluded if they are set to an empty array:
     *  - attributes
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Appearance for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($data === null) {
            if ($array['id'] === null) {
                unset($array['id']);
            }
            if (empty($array['attributes'])) {
                unset($array['attributes']);
            }
            if ($array['relationships'] === null) {
                unset($array['relationships']);
            }
        }
        return $array;
    }
}
