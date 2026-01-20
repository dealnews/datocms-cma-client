<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Webhook;

use Moonspot\ValueObjects\ValueObject;

/**
 * Defines attributes for a webhook
 *
 * This class is only needed when you want to create/update a webhook using a webhook API request.
 * You will need to initialize the class, set your properties, and then set the resulting object on the Webhook::attributes property.
 *
 * Usage:
 *  ```php
 *  $webhook_input = new Webhook();
 *
 *  $attributes = new Attributes();
 *  $attributes->name = 'Hello';
 *
 *  $webhook_input->attributes = $attributes;
 *  ```
 */
class Attributes extends ValueObject {

    /**
     * Unique name for the webhook
     *
     * Required for creating a new webhook.
     * Optional for updates. Setting to null will exclude this from the request
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * The URL to be called
     *
     * Required for creating a new webhook.
     * Optional for updates. Setting to null will exclude this from the request
     *
     * @var string|null
     */
    public ?string $url = null;

    /**
     * Additional headers that will be sent
     *
     * Should be an associative array where header names are keys and header values are array values.
     *
     * Required for creating a new webhook (can be an empty array).
     * Optional for updates. Setting to null will exclude this from the request
     *
     * @var array|null
     */
    public ?array $headers = null;

    /**
     * A list of events that would trigger this webhook
     *
     * Can either provide a properly structured associative array or an instance of Events
     *
     * Required for creating a new webhook.
     * Optional for updates. Setting to null will exclude this from the request
     */
    public array|Events|null $events = null;

    /**
     * A custom payload
     *
     * Example: '{ "message": "{{event_type}} event triggered on {{entity_type}}!", "entity_id": "{{#entity}}{{id}}{{/entity}}" }'
     *
     * Required for creating a new webhook.
     * Optional for updates. Setting to false will exclude this from the request
     *
     * @var string|null|false
     */
    public string|null|false $custom_payload = false;

    /**
     * HTTP Basic Authorization username
     *
     * Required for creating a new webhook.
     * Optional for updates. Setting to false will exclude this from the request
     *
     * @var string|null|false
     */
    public string|null|false $http_basic_user = false;

    /**
     * HTTP Basic Authorization password
     *
     * Required for creating a new webhook.
     * Optional for updates. Setting to false will exclude this from the request
     *
     * @var string|null|false
     */
    public string|null|false $http_basic_password = false;

    /**
     * Whether the webhook is enabled and sending events or not
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var bool|null
     */
    public ?bool $enabled = null;

    /**
     * Specifies which API version to use when serializing entities in the webhook payload
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var string|null
     */
    public ?string $payload_api_version = null;

    /**
     * Whether you want records present in the payload to show blocks expanded or not
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var bool|null
     */
    public ?bool $nested_items_in_payload = null;

    /**
     * If true, the system will attempt to retry the call several times when the webhook operation fails due to timeouts or errors.
     *
     * Optional. Setting to null will exclude this from the request
     *
     * @var bool|null
     */
    public ?bool $auto_retry = null;

    /**
     * Converts to API array format
     *
     * Will exclude name, url, headers, events, enabled, payload_api_version, nested_items_in_payload, and auto_retry from output if set to null
     * Will exclude custom_payload, http_basic_user, and http_basic_password from output if set to false
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Webhook Attributes for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($data === null) {
            if ($array['name'] === null) {
                unset($array['name']);
            }
            if ($array['url'] === null) {
                unset($array['url']);
            }
            if ($array['headers'] === null) {
                unset($array['headers']);
            }
            if ($array['events'] === null) {
                unset($array['events']);
            }
            if ($array['custom_payload'] === false) {
                unset($array['custom_payload']);
            }
            if ($array['http_basic_user'] === false) {
                unset($array['http_basic_user']);
            }
            if ($array['http_basic_password'] === false) {
                unset($array['http_basic_password']);
            }
            if ($array['enabled'] === null) {
                unset($array['enabled']);
            }
            if ($array['payload_api_version'] === null) {
                unset($array['payload_api_version']);
            }
            if ($array['nested_items_in_payload'] === null) {
                unset($array['nested_items_in_payload']);
            }
            if ($array['auto_retry'] === null) {
                unset($array['auto_retry']);
            }
        }

        return $array;
    }
}
