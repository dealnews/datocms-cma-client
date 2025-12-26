<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Relationships;

use Moonspot\ValueObjects\ValueObject;

/**
 * Creator relationship for a DatoCMS record
 *
 * Optionally specifies who created the record. If both type and id are null,
 * DatoCMS determines the creator automatically.
 *
 * Valid type values: 'account', 'access_token', 'user', 'sso_user', 'organization'
 *
 * Usage:
 * ```php
 * $record->relationships->creator->type = 'user';
 * $record->relationships->creator->id = 'user-id-123';
 * ```
 */
class Creator extends ValueObject {

    /**
     * Creator type
     *
     * Valid values: 'account', 'access_token', 'user', 'sso_user', 'organization'
     * If null (with id also null), DatoCMS sets creator automatically.
     *
     * @var string|null
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
     * Creator ID
     *
     * The unique identifier for the creator of the specified type.
     * If null (with type also null), DatoCMS sets creator automatically.
     *
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Converts to API array format
     *
     * Returns wrapped {data: {...}} structure if both type and id are set,
     * otherwise returns empty array (creator is excluded).
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Creator relationship for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if (!empty($array['type']) && !empty($array['id'])) {
            return ['data' => $array];
        }
        return [];
    }
}