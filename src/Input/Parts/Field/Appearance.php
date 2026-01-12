<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Field;

use Moonspot\ValueObjects\ValueObject;
use DealNews\DatoCMS\CMA\Input\Parts\Field\Appearance\AddOn;

class Appearance extends ValueObject {

    /**
     * A valid editor can be a DatoCMS default field editor type (ie. "single_line"), or a plugin ID offering a custom field editor
     *
     * @var string
     */
    public string $editor = '';

    /**
     * The editor plugin's parameters
     *
     * @var array<string, mixed>
     */
    public array $parameters = [];

    /**
     * An array of add-on plugins with id and parameters
     *
     * @var AddOn[]
     */
    public array $addons = [];

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
     * @return array<string, mixed> Appearance for API submission
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
