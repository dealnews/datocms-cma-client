<?php

namespace DealNews\DatoCMS\CMA\Input;

use DealNews\DatoCMS\CMA\Input\Parts\Relationships\ItemType;
use Moonspot\ValueObjects\ValueObject;

/**
 * Input object for creating and updating DatoCMS model filters
 *
 * Model filters are saved searches that help editors quickly find records
 * matching specific criteria. Each filter is associated with a model (item_type).
 *
 * Usage:
 * ```php
 * $filter = new ModelFilter('model-id');
 * $filter->attributes['name'] = 'Draft posts';
 * $filter->attributes['filter'] = [
 *     'query' => 'foo bar',
 *     'fields' => ['_status' => ['eq' => 'draft']],
 * ];
 * $filter->attributes['shared'] = true;
 * $result = $client->model_filter->create($filter);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item-type-filter
 */
class ModelFilter extends ValueObject {

    /**
     * Optional UUID for the filter
     *
     * When creating, leave null to auto-generate. When updating, this is
     * typically not needed as the ID is passed to the update method.
     *
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Filter type, must always be "item_type_filter"
     *
     * WARNING: This property MUST be set to "item_type_filter". Setting any
     * other value will cause API errors. Do not modify this property.
     *
     * @var string
     */
    public readonly string $type;

    /**
     * Filter configuration attributes
     *
     * Common attributes include:
     * - `name` (string, required): Human-readable filter name
     * - `filter` (array): Filter criteria matching Record list filter format
     * - `columns` (array): Columns to display [{name, width}, ...]
     * - `order_by` (string): Sort order, e.g., "_updated_at_ASC"
     * - `shared` (bool): Whether filter is visible to all editors
     *
     * @var array<string, mixed>
     */
    public array $attributes = [];

    /**
     * Item type (model) relationship
     *
     * Specifies which model this filter belongs to.
     *
     * @var ItemType
     */
    public ItemType $item_type;

    /**
     * Creates a new ModelFilter input object
     *
     * @param string|null $item_type_id The item_type (model) ID for this filter
     */
    public function __construct(?string $item_type_id = null) {
        $this->item_type = new ItemType();

        if (!empty($item_type_id)) {
            $this->item_type->id = $item_type_id;
        }
        $this->type = 'item_type_filter';
    }

    /**
     * Sets the item type (model) ID for this filter
     *
     * @param string $item_type_id The model ID
     *
     * @return self For method chaining
     */
    public function setItemType(string $item_type_id): self {
        $this->item_type->id = $item_type_id;

        return $this;
    }

    /**
     * Gets the item type (model) ID for this filter
     *
     * @return string The model ID
     */
    public function getItemTypeId(): string {
        return $this->item_type->id;
    }

    /**
     * Converts the filter to an array for API submission
     *
     * Excludes empty id and attributes. Includes relationships when item_type
     * ID is set.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> API-ready array structure
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);

        // Only process at the top level (when $data is null)
        // Recursive calls from parent pass $data as an array
        if ($data !== null) {
            return $array;
        }

        if (empty($array['id'])) {
            unset($array['id']);
        }
        if (empty($array['attributes'])) {
            unset($array['attributes']);
        }

        // Remove the raw item_type serialization from parent - we handle it
        // specially as a nested relationship structure
        unset($array['item_type']);

        // Build relationships structure if item_type ID is set
        if (!empty($this->item_type->id)) {
            $array['relationships'] = [
                'item_type' => $this->item_type->toArray(),
            ];
        }

        return $array;
    }
}
