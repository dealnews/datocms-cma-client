<?php

namespace DealNews\DatoCMS\CMA\Input;

use DealNews\DatoCMS\CMA\Input\Parts\Meta;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships;
use Moonspot\ValueObjects\Interfaces\Export;
use Moonspot\ValueObjects\ValueObject;

class Record extends ValueObject {

    /**
     * Optional.
     *
     * An uuid for this record/item
     */
    public ?string $id = null;

    /**
     * Should always be "item"
     */
    public string $type = 'item' {
        set {
            if ($value !== 'item') {
                throw new \InvalidArgumentException('Type must be "item"');
            }
            $this->type = $value;
        }
    }

    /**
     * An associative array of field-names and field-values to set for this record/item
     */
    public array $attributes = [];

    /**
     * Optional.
     *
     * Set certain "meta" values for the record (like "created_at", "first_published_at", etc...)
     *
     * If no properties are set on this object, then the "meta" object will be excluded from the API input data
     */
    public Meta $meta;

    /**
     * Defines relationships for this record such as what "item_type" it should belong to and information
     * on who was the original "creator" of this record (creator is optional)
     */
    public Relationships $relationships;

    public function __construct(?string $item_type_id = null) {
        $this->meta = new Meta();
        $this->relationships = new Relationships();

        if (!empty($item_type_id)) {
            $this->relationships->item_type->id = $item_type_id;
        }
    }

    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['id'])) {
            unset($array['id']);
        }
        if (empty($array['meta'])) {
            unset($array['meta']);
        }
        if (empty($array['attributes'])) {
            unset($array['attributes']);
        } else {
            foreach ($array['attributes'] as $attribute_name => $attribute_value) {
                if (is_object($attribute_value)) {
                    if ($attribute_value instanceof Export) {
                        $array['attributes'][$attribute_name] = $attribute_value->toArray();
                    } elseif ($attribute_value instanceof \JsonSerializable) {
                        $array['attributes'][$attribute_name] = $attribute_value->jsonSerialize();
                    } else {
                        throw new \LogicException("Attribute '$attribute_name' is an object and does not implement the Export or JsonSerializable interface");
                    }
                }

            }
        }
        return $array;
    }
}
