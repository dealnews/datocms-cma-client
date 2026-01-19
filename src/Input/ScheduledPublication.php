<?php

namespace DealNews\DatoCMS\CMA\Input;

use Moonspot\ValueObjects\ValueObject;

/**
 * Input object for creating and updating DatoCMS publication schedules for records/items
 *
 * Represents the data structure for "ScheduledPublication" operations. "ScheduledPublication" defines
 * the date/time you wish a record/item to be published (in the future).
 *
 * Usage:
 * ```php
 * $scheduled_publication = new ScheduledPublication();
 * $scheduled_publication->attributes['publication_scheduled_at'] = '2025-01-01T10:00:00Z';
 * $scheduled_publication->attributes['selective_publication'] = [
 *     'content_in_locales' => ['en', 'it'],
 *     'non_localized_content' => true,
 * ];
 * $result = $client->scheduled_publication->create('record_id', $scheduled_publication);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/scheduled-publication/create
 */
class ScheduledPublication extends ValueObject {

    /**
     * Scheduled Publication type, must always be "scheduled_publication"
     *
     * WARNING: This property MUST be set to "scheduled_publication". Setting any other value
     * will cause API errors. Do not modify this property.
     *
     * @var string
     */
    public readonly string $type;

    /**
     * "ScheduledPublication" configuration attributes
     *
     * Possible attributes include:
     * - `publication_scheduled_at` (string): REQUIRED. Date/time to publish record (in the future). Must be in ISO 8601 format.
     * - `selective_publication` (array<string, string|bool>): OPTIONAL. Describes what locales are to be published at the scheduled time. Is required to include the following keys:
     *      - `content_in_locales` (array<string>): List of locales whose content will be published
     *      - `non_localized_content` (bool): Whether the non-localized content has to be published or not
     *
     * @var array<string, mixed>
     */
    public array $attributes = [];

    public function __construct() {
        $this->type = 'scheduled_publication';
    }

    /**
     * Converts the ScheduledPublication to an array for API submission
     *
     * Removes the `non_localized_content` attribute when it is empty.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> API-ready array structure
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['attributes']['non_localized_content'])) {
            unset($array['attributes']['non_localized_content']);
        }
        return $array;
    }
}
