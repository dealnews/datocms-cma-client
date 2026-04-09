<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Plugin;

use Moonspot\ValueObjects\ValueObject;

class PrivateAttributes extends ValueObject {

    /**
     * The name of the plugin.
     *
     * @var string
     */
    public string $name = '';

    /**
     * A description of the plugin.
     *
     * Optional. Setting to false will exclude this from the request
     *
     * @var null|false|string
     */
    public null|false|string $description = false;

    /**
     * The entry point URL of the plugin
     *
     * Example: "https://cdn.rawgit.com/datocms/extensions/master/samples/five-stars/extension.ts"
     *
     * @var string
     */
    public string $url = '';

    /**
     * Permissions granted to this plugin.
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var null|array<string>
     */
    public ?array $permissions = null;

    /**
     * {@inheritdoc}
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($data === null) {
            if ($array['description'] === false) {
                unset($array['description']);
            }
            if ($array['permissions'] === null) {
                unset($array['permissions']);
            }
        }
        return $array;
    }
}
