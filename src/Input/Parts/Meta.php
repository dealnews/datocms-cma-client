<?php

namespace DealNews\DatoCMS\CMA\Input\Parts;

use Moonspot\ValueObjects\ValueObject;

class Meta extends ValueObject {

    /**
     * Optional
     *
     * Set a "created_at" date/time (in ISO 8601 date-time format).
     *
     * If "null", DatoCMS will set a created_at automatically (if not already set)
     */
    public ?string $created_at = null;

    /**
     * Optional
     *
     * Set a "first_published_at" date/time (in ISO 8601 date-time format).
     *
     * If "false", DatoCMS will set a value automatically (if not already set and it makes sense to do so).
     * If "null", will force DatoCMS to unset this value if it's been set, already
     *
     * // TODO: check "null" logic to make sure above docblock is correct
     */
    public string|null|false $first_published_at = false;

    /**
     * Optional
     *
     * If set to something besides "null", DatoCMS's "optimistic locking" feature will be used to make sure
     * you're updating the expected version of the record/item
     *
     * @see https://www.datocms.com/docs/content-management-api/resources/item/update?language=http#optimistic-locking
     */
    public ?string $current_version = null;

    /**
     * Optional
     *
     * If "false", DatoCMS will set a value automatically (if not already set and it makes sense to do so)
     * If "null", will force DatoCMS to unset this value if it's been set, already
     *
     * // TODO: check "null" logic to make sure above docblock is correct
     */
    public string|null|false $stage = false;

    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (empty($array['created_at'])) {
            unset($array['created_at']);
        }
        if ($array['first_published_at'] === false) {
            unset($array['first_published_at']);
        }
        if (empty($array['current_version'])) {
            unset($array['current_version']);
        }
        if ($array['stage'] === false) {
            unset($array['stage']);
        }
        return $array;
    }
}