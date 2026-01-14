<?php

namespace DealNews\DatoCMS\CMA\Input;

use Moonspot\ValueObjects\ValueObject;

use DealNews\DatoCMS\CMA\Input\Parts\Webhook\Attributes;

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
     * Webhook type, always "webhook"
     *
     * Enforced by setter - attempting to set any other value throws an exception.
     *
     * @var string
     */
    public string $type = 'webhook' {
        set {
            if ($value !== 'webhook') {
                throw new \InvalidArgumentException('Type must be "webhook"');
            }
            $this->type = $value;
        }
    }

    /**
     * Attributes for the webhook
     *
     * @var array|Attributes
     */
    public array|Attributes $attributes = [];

    /**
     * Converts the webhook to an array for API submission
     *
     * Excludes empty id.
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
        }
        return $array;
    }
}
