<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Field\Appearance;

use Moonspot\ValueObjects\ValueObject;

/**
 * Represents a field plugin "addon" that affects the field's appearance
 *
 * Usage:
 *  ```php
 *  $addon = new AddOn();
 *  $addon->id = 'plugin-id';
 *  $addon->parameters = ['param1' => 'value1', 'param2' => 'value2'];
 *  $addon->field_extension = 'extension-id';
 *
 *  $appearance = new \DealNews\DatoCMS\CMA\Input\Parts\Field\Appearance();
 *  $appearance->addons[] = $addon;
 *  ```
 */
class AddOn extends ValueObject {

    /**
     * The ID of a plugin offering a field addon
     *
     * @var string
     */
    public string $id = '';

    /**
     * The plugin's parameters
     *
     * @var array<string, mixed>
     */
    public array $parameters = [];

    /**
     * The specific field extension to use for the field (only if the editor is a modern plugin)
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var string|null
     */
    public ?string $field_extension = null;

    /**
     * Converts to API array format
     *
     * Returns an array with field_extension excluded if it was set to null
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> AddOn for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($data === null) {
            if ($array['field_extension'] === null) {
                unset($array['field_extension']);
            }
        }
        return $array;
    }
}
