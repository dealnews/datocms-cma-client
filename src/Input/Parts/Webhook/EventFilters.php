<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Webhook;

/**
 * An object representing a set of event filters for a webhook trigger requirement.
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/webhook
 *
 * Usage:```php
 * $attributes = new Attributes();
 *
 * $event_filters = EventFilters::init()
 *                      ->addFilter('environment', ['main'])
 *                      ->addFilter('item_type', ['1234']);
 *
 * $attributes->events = Events::init()->addEvent('item', ['create'], $event_filters);
 * ```
 *
 * @suppress PhanRedefinedInheritedInterface
 */
class EventFilters implements \JsonSerializable {

    const array VALID_ENTITY_TYPE = [
        'item_type',
        'item',
        'build_trigger',
        'environment',
        'environment_type'
    ];

    protected array $filters = [];

    public static function init(): static {
        return new static();
    }

    /**
     * Add an event filter that an event must match in order for the webhook to trigger.
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook
     *
     * @param   string  $type           The type of entity to filter on. Must be one of VALID_ENTITY_TYPE.
     * @param   array   $entity_ids     The IDs of the entities to filter on.
     *                                  For entity_type of "environment", this would be the environment id/name,
     *                                  For entity_type of "item_type", this would be the model id,
     *                                  etc...
     * @return  static
     */
    public function addFilter(string $type, array $entity_ids): static {
        if (!in_array($type, self::VALID_ENTITY_TYPE)) {
            throw new \InvalidArgumentException("Event type must be one of " . implode(", ", self::VALID_ENTITY_TYPE));
        }
        $this->filters[] = [
            'entity_type' => $type,
            'entity_ids' => $entity_ids
        ];
        return $this;
    }

    /**
     * Serializes the value for JSON encoding
     *
     * Returns the filters array
     *
     * @return  array   Serialized value
     */
    function jsonSerialize(): array {
        return $this->filters;
    }
}
