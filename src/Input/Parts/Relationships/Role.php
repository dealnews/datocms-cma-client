<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Relationships;

use Moonspot\ValueObjects\ValueObject;

/**
 * Role relationship for various DatoCMS objects
 *
 * Usage:
 * ```php
 * $role = new Role();
 * $role->id = '24';
 *
 * $site->relationships->role = $role;
 * ```
 */
class Role extends ValueObject {

    /**
     * Role type, can only be set to "role"
     *
     * @var string
     */
    public string $type = 'role' {
        set {
            if ($value !== 'role') {
                throw new \InvalidArgumentException('Type must be "role"');
            }
            $this->type = $value;
        }
    }

    /**
     * Role ID
     *
     * The unique identifier for the DatoCMS role.
     *
     * @var string
     */
    public string $id = '';

    /**
     * Converts to API array format
     *
     * Wraps type and id in {data: {...}} structure as required by the API.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Role type relationship for API submission
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        return ['data' => $array];
    }
}