<?php

namespace DealNews\DatoCMS\CMA\Input;

use DealNews\DatoCMS\CMA\Input\Parts\Webhook\Attributes;

use Moonspot\ValueObjects\ValueObject;

class Webhook extends ValueObject {

    /**
     * The ID of the webhook
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Webhook type, must always be "webhook"
     *
     * WARNING: This property MUST be set to "webhook". Setting any other value
     * will cause API errors. Do not modify this property.
     *
     * @var string
     */
    public readonly string $type;

    /**
     * Attributes for the webhook
     *
     * @var array|Attributes
     */
    public array|Attributes $attributes = [];

    public function __construct() {
        $this->type = 'webhook';
    }

    /**
     * Converts the webhook to an array for API submission
     *
     * Excludes empty id and empty attributes.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> API-ready array structure
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
}
