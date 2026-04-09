<?php

namespace DealNews\DatoCMS\CMA\Input;

use Moonspot\ValueObjects\ValueObject;
use DealNews\DatoCMS\CMA\Input\Parts\Plugin\PrivateAttributes;
use DealNews\DatoCMS\CMA\Input\Parts\Plugin\PublicAttributes;

/**
 * Input object for creating and updating DatoCMS plugin settings
 *
 * Represents the data structure for plugin operations.
 *
 * Usage:
 * ```php
 * $plugin = new Plugin();
 * $plugin->attributes['package_name'] = 'npm-package-name';
 * $result = $client->plugin->create($plugin);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/plugin/create
 */
class Plugin extends ValueObject {

    /**
     * RFC 4122 UUID of the plugin expressed in URL-safe base64 format
     *
     * Optional. Setting to null will exclude this from the request.
     *
     * @var null|string
     */
    public ?string $id = null;

    /**
     * Must be exactly "plugin".
     *
     * @var string
     */
    public string $type = 'plugin';

    /**
     * {@inheritdoc}
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
        }
        return $array;
    }

    /**
     * Attributes/settings for this plugin
     *
     * If you're creating/updating a public plugin, all you need is the 'package_name' attribute (or use the PublicAttributes object).
     * If you're creating/updating a private plugin, you need to provide more/different settings/attributes. Refer to the documentation or use the PrivateAttributes object.
     *
     * @var array<string, mixed>|PrivateAttributes|PublicAttributes
     */
    public array|PrivateAttributes|PublicAttributes $attributes = [];
}
