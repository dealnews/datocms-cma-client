<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\FieldSet;

use Moonspot\ValueObjects\ValueObject;

/**
 * Defines attributes for a fieldset
 *
 * This class is only needed when you want to create/update a fieldset using a fieldset API request.
 * You will need to initialize the class, set your properties, and then set the resulting object on the FieldSet::attributes property.
 *
 * Usage:
 *  ```php
 *  $fieldset_input = new FieldSet();
 *
 *  $attributes = new Attributes();
 *  $attributes->title = 'Hello';
 *
 *  $fieldset_input->attributes = $attributes;
 *  ```
 */
class Attributes extends ValueObject {

    /**
     * The title of the fieldset
     *
     * Required to be a string when creating a fieldset
     * Optional when updating a fieldset. Setting to null will exclude this from the request.
     *
     * @var string|null
     */
    public ?string $title = null;

    /**
     * Description/contextual hint for the fieldset
     *
     * Example: "Please fill in these fields!"
     *
     * Optional. Setting to false will exclude this from the request
     *
     * @var string|null|false
     */
    public string|null|false $hint = false;

    /**
     * Ordering index
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var int|null
     */
    public ?int $position = null;

    /**
     * Whether the fieldset can be collapsed or not
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var bool|null
     */
    public ?bool $collapsible = null;

    /**
     * When the fieldset is collapsible, determines if the default is to start collapsed or not
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var bool|null
     */
    public ?bool $start_collapsed = null;

    /**
     * Converts to API array format
     *
     * Will exclude title, position, collapsible, and start_collapsed from output if set to null
     * Will exclude hint from output if set to false
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> FieldSet Attributes for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($data === null) {
            if ($array['title'] === null) {
                unset($array['title']);
            }
            if ($array['hint'] === false) {
                unset($array['hint']);
            }
            if ($array['position'] === null) {
                unset($array['position']);
            }
            if ($array['collapsible'] === null) {
                unset($array['collapsible']);
            }
            if ($array['start_collapsed'] === null) {
                unset($array['start_collapsed']);
            }
        }

        return $array;
    }
}
