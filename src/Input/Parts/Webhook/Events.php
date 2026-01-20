<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Webhook;

/**
 * An object representing a set of events for a webhook trigger requirement.
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/webhook
 *
 * Usage: ```php
 * $attributes = new Attributes();
 *
 * $attributes->events = Events::init()
 *                          ->addEvent('item', ['create', 'update'])
 *                          ->addEvent('upload', ['create', 'update'], EventFilters::init()->addFilter('environment_type', ['primary']));
 * ```
 *
 * @suppress PhanRedefinedInheritedInterface
 */
class Events implements \JsonSerializable {

    const VALID_ENTITY_TYPE = [
        'item_type',
        'item',
        'upload',
        'build_trigger',
        'environment',
        'maintenance_mode',
        'sso_user',
        'cda_cache_tags',
    ];

    protected array $events = [];

    public static function init(): static {
        return new static();
    }

    /**
     * Add an event that will trigger the webhook
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook
     *
     * @param   string                      $type       The subject of webhook triggering
     * @param   array                       $events     The event (create, publish, delete, etc...)
     * @param   array|EventFilters|null     $filters    Optional filters to apply. Can either provide a properly structured
     *                                                  associative array or an instance of EventFilters
     *
     * @return  static
     */
    public function addEvent(string $type, array $events, array|EventFilters|null $filters = null): static {
        if (!in_array($type, self::VALID_ENTITY_TYPE)) {
            throw new \InvalidArgumentException('Event type must be one of ' . implode(', ', self::VALID_ENTITY_TYPE));
        }
        $event = [
            'event_type'  => $type,
            'event_types' => $events,
        ];
        if ($filters !== null) {
            if (!is_array($filters)) {
                $filters = $filters->jsonSerialize();
            }
            $event['filters'] = $filters;
        }
        $this->events[] = $event;

        return $this;
    }

    /**
     * Serializes the value for JSON encoding
     *
     * Returns the events array
     *
     * @return  array   Serialized value
     */
    public function jsonSerialize(): array {
        return $this->events;
    }
}
