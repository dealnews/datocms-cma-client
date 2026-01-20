<?php

namespace DealNews\DatoCMS\CMA\Parameters\Parts;

use Moonspot\ValueObjects\ValueObject;

/**
 * Filter conditions for webhook call queries
 *
 * Allows for filtering based on webhook call ids or webhook call fields
 *
 * Usage:
 * ```php
 * $params->filter->fields->addField('webhook_id', '123', 'eq');
 * $params->filter->fields->addField('status', 'pending');
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/webhook-call/instances
 */
class WebhookCallFilter extends ValueObject {

    /**
     * IDs to fetch
     *
     * Optional.
     *
     * @var string[]|null
     */
    public ?array $ids = null;

    /**
     * Field-level filter conditions
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/webhook-call/instances
     *
     * "eq" is the only supported operator for MOST filtering field instances.
     *
     * Possible fields to filter on:
     * - webhook_id
     * - entity_type
     * - event_type
     * - status (pending, success, failed, rescheduled)
     * - last_sent_at (supported operators: gt or lt)
     * - next_retry_at (supported operators: gt or lt)
     * - created_at (supported operators: gt or lt)
     *
     * @var FilterFields
     */
    public FilterFields $fields;

    /**
     * Initializes the fields filter object
     */
    public function __construct() {
        $this->fields = new FilterFields();
    }

    /**
     * Converts filter to query parameters
     *
     * Excludes empty values and joins array values with commas.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Filter query parameters
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        foreach ($array as $key => $value) {
            if (empty($value)) {
                unset($array[$key]);
            } elseif ($key === 'ids') {
                $array[$key] = implode(',', $value);
            }
        }

        return $array;
    }
}
