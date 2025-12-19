<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Relationships;

use Moonspot\ValueObjects\ValueObject;

class Creator extends ValueObject {

    /**
     * Optional.
     *
     * If "null" (or id is "null"), DatoCMS will automatically set the "creator" information for this record/item
     * If not "null", valid values are: "account", "access_token", "user", "sso_user", "organization"
     */
    public ?string $type = null {
        set {
            if (!is_null($value) && !in_array($value, ['account', 'access_token', 'user', 'sso_user', 'organization'])) {
                throw new \InvalidArgumentException('Type must be null or one of "account", "access_token", "user", "sso_user", "organization"');
            }
            $this->type = $value;
        }
    }

    /**
     * Optional.
     *
     * If "null" (or type is "null"), DatoCMS will automatically set the "creator" information for this record/item.
     * If not "null", this "id" should be the unique id for whatever "type" is provided in the "type" property.
     */
    public ?string $id = null;

    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (!empty($array['type']) && !empty($array['id'])) {
            return ['data' => $array];
        }
        return [];
    }
}