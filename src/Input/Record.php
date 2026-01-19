<?php

namespace DealNews\DatoCMS\CMA\Input;

use DealNews\DatoCMS\CMA\Input\Parts\Meta;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships;
use Moonspot\ValueObjects\Interfaces\Export;
use Moonspot\ValueObjects\ValueObject;

/**
 * Input object for creating and updating DatoCMS records/items
 *
 * Represents the data structure for record operations. Supports both
 * scalar attributes and DataType objects for field values.
 *
 * Usage:
 * ```php
 * $record = new Record('item-type-id');
 * $record->attributes['title'] = 'Hello World';
 * $record->attributes['color'] = Color::init()->setColor(255, 0, 0, 255);
 * $result = $client->record->create($record);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item/create
 */
class Record extends ValueObject {

    /**
     * Optional UUID for the record/item
     *
     * When creating, leave null to auto-generate. When updating, this is
     * typically not needed as the ID is passed to the update method.
     *
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Record type, must always be "item"
     *
     * WARNING: This property MUST be set to "item". Setting any other value
     * will cause API errors. Do not modify this property.
     *
     * @var string
     */
    public string $type = 'item';

    /**
     * Field values for the record
     *
     * Keys are field API names, values can be scalars, arrays, or DataType objects.
     * DataType objects are automatically serialized via Export or JsonSerializable.
     *
     * @var array<string, mixed>
     */
    public array $attributes = [];

    /**
     * Record metadata (created_at, first_published_at, current_version, stage)
     *
     * Excluded from output if no properties are set.
     *
     * @var Meta
     */
    public Meta $meta;

    /**
     * Record relationships (item_type and optional creator)
     *
     * @var Relationships
     */
    public Relationships $relationships;

    /**
     * Creates a new Record input object
     *
     * @param string|null $item_type_id The item_type (model) ID for this record
     */
    public function __construct(?string $item_type_id = null) {
        $this->meta = new Meta();
        $this->relationships = new Relationships();

        if (!empty($item_type_id)) {
            $this->relationships->item_type->id = $item_type_id;
        }
    }

    /**
     * Converts the record to an array for API submission
     *
     * Excludes empty id, meta, and attributes. Serializes DataType objects
     * in attributes via their Export or JsonSerializable interfaces.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> API-ready array structure
     *
     * @throws \LogicException If an attribute object lacks Export/JsonSerializable
     */
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
