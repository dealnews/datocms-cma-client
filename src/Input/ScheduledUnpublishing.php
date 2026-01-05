<?php

namespace DealNews\DatoCMS\CMA\Input;

use Moonspot\ValueObjects\ValueObject;

/**
 * Input object for creating DatoCMS unpublication schedules for records/items
 *
 * Represents the data structure for "ScheduledUnpublishing" operations. "ScheduledUnpublishing" defines
 * the date/time you wish a record/item to be unpublished (in the future).
 *
 * Usage:
 * ```php
 * $scheduled_unpublishing = new ScheduledUnpublishing();
 * $scheduled_unpublishing->attributes['unpublishing_scheduled_at'] = '2025-01-01T10:00:00Z';
 * $scheduled_unpublishing->attributes['content_in_locales'] = ['en', 'it'];
 * $result = $client->scheduled_unpublishing->create('record_id', $scheduled_unpublishing);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/scheduled-publication/create
 */
class ScheduledUnpublishing extends ValueObject {

    /**
     * Scheduled Unpublication type, always "scheduled_unpublishing"
     *
     * Enforced by setter - attempting to set any other value throws an exception.
     *
     * @var string
     */
    public string $type = 'scheduled_unpublishing' {
        set {
            if ($value !== 'scheduled_unpublishing') {
                throw new \InvalidArgumentException('Type must be "scheduled_unpublishing"');
            }
            $this->type = $value;
        }
    }

    /**
     * "ScheduledUnpublishing" configuration attributes
     *
     * Possible attributes include:
     * - `unpublishing_scheduled_at` (string): REQUIRED. Date/time to unpublish record (in the future). Must be in ISO 8601 format.
     * - `content_in_locales` (array<string>): OPTIONAL. List of locales whose content will be unpublished
     *
     * @var array<string, mixed>
     */
    public array $attributes = [];

    /**
     * Converts the ScheduledUnpublishing to an array for API submission
     *
     * Removes the `content_in_locales` attribute when it is empty.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> API-ready array structure
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['attributes']['content_in_locales'])) {
            unset($array['attributes']['content_in_locales']);
        }
        return $array;
    }
}