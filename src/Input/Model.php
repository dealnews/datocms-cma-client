<?php

namespace DealNews\DatoCMS\CMA\Input;

use Moonspot\ValueObjects\ValueObject;

/**
 * Input object for creating and updating DatoCMS models (item-types)
 *
 * Represents the data structure for model operations. Models define the
 * content types in your DatoCMS project (e.g., blog posts, products).
 *
 * Usage:
 * ```php
 * $model = new Model();
 * $model->attributes['name'] = 'Blog Post';
 * $model->attributes['api_key'] = 'blog_post';
 * $model->attributes['singleton'] = false;
 * $result = $client->model->create($model);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/item-type/create
 */
class Model extends ValueObject {

    /**
     * Optional UUID for the model
     *
     * When creating, leave null to auto-generate. When updating, this is
     * typically not needed as the ID is passed to the update method.
     *
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Model type, must always be "item_type"
     *
     * WARNING: This property MUST be set to "item_type". Setting any other value
     * will cause API errors. Do not modify this property.
     *
     * @var string
     */
    public readonly string $type;

    /**
     * Model configuration attributes
     *
     * Common attributes include:
     * - `name` (string): Human-readable name
     * - `api_key` (string): Machine-friendly key
     * - `singleton` (bool): Single-instance model
     * - `sortable` (bool): Allow manual record sorting
     * - `modular_block` (bool): Is a block model
     * - `tree` (bool): Hierarchical records
     * - `draft_mode_active` (bool): Enable drafts
     * - `all_locales_required` (bool): Require all locales
     * - `ordering_direction` (string): 'asc' or 'desc'
     * - `ordering_meta` (string): Field to order by
     * - `collection_appearance` (string): 'compact' or 'table'
     * - `hint` (string): Editor hint text
     *
     * @var array<string, mixed>
     */
    public array $attributes = [];

    public function __construct() {
        $this->type = 'item_type';
    }

    /**
     * Converts the model to an array for API submission
     *
     * Excludes empty id and attributes.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> API-ready array structure
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['id'])) {
            unset($array['id']);
        }
        if (empty($array['attributes'])) {
            unset($array['attributes']);
        }
        return $array;
    }
}
