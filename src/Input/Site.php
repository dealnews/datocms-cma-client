<?php

namespace DealNews\DatoCMS\CMA\Input;

use Moonspot\ValueObjects\ValueObject;

use DealNews\DatoCMS\CMA\Input\Parts\Site\Attributes;
use DealNews\DatoCMS\CMA\Input\Parts\Site\Meta;
use DealNews\DatoCMS\CMA\Input\Parts\Site\Relationships;

/**
 * Input object for updating DatoCMS site information/settings
 *
 * Represents the data structure for site operations.
 *
 * Usage:
 * ```php
 * $site = new Site();
 *
 * $attributes = new Attributes();
 * $attributes->no_index = true;
 *
 * $site->attributes = $attributes;
 * $result = $client->site->update($site);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/site/update#body-parameters
 */
class Site extends ValueObject {

    /**
     * Input type, always "site"
     *
     * Enforced by setter - attempting to set any other value throws an exception.
     *
     * @var string
     */
    public string $type = 'site' {
        set {
            if ($value !== 'site') {
                throw new \InvalidArgumentException('Type must be "site"');
            }
            $this->type = $value;
        }
    }

    /**
     * Site attributes to update
     *
     * Can provide an associative array or use the Attributes class.
     *
     * Optional.
     *
     * @var array<string, mixed>|Attributes
     */
    public array|Attributes $attributes = [];

    /**
     * Site meta data to update
     *
     * Can provide an associative array or use the Meta class.
     *
     * Optional.
     *
     * @var array<string, mixed>|Meta
     */
    public array|Meta $meta = [];

    /**
     * Relationships between the site and other entities
     *
     * Optional. Setting to null will exclude this from the update request
     *
     * @var null|Relationships
     */
    public ?Relationships $relationships = null;

    /**
     * Converts to API array format
     *
     * Will exclude attributes, meta, and relationships from output if they are empty
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Site Input for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['attributes'])) {
            unset($array['attributes']);
        }
        if (empty($array['meta'])) {
            unset($array['meta']);
        }
        if (empty($array['relationships'])) {
            unset($array['relationships']);
        }
        return $array;
    }
}
