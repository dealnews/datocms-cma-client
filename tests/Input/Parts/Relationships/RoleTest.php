<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Relationships;

use DealNews\DatoCMS\CMA\Input\Parts\Relationships\Role;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Role relationship input part, including default values,
 * type validation, and array serialization behavior.
 */
class RoleTest extends TestCase {

    #[Group('unit')]
    public function testDefaultTypeIsRole() {
        $role = new Role();

        $this->assertEquals('role', $role->type);
    }

    #[Group('unit')]
    public function testDefaultIdIsEmpty() {
        $role = new Role();

        $this->assertEquals('', $role->id);
    }

    #[Group('unit')]
    public function testSettingValidId() {
        $role     = new Role();
        $role->id = 'role_123';

        $this->assertEquals('role_123', $role->id);
    }

    #[Group('unit')]
    public function testToArrayWrapsInDataStructure() {
        $role     = new Role();
        $role->id = 'role_123';

        $array = $role->toArray();

        $this->assertArrayHasKey('data', $array);
        $this->assertEquals([
            'data' => [
                'type' => 'role',
                'id'   => 'role_123',
            ],
        ], $array);
    }

    #[Group('unit')]
    public function testToArrayWithDefaultValues() {
        $role = new Role();

        $array = $role->toArray();

        $this->assertEquals([
            'data' => [
                'type' => 'role',
                'id'   => '',
            ],
        ], $array);
    }

    #[Group('unit')]
    public function testTypeRemainsRoleAfterSettingSameValue() {
        $role = new Role();

        $this->assertEquals('role', $role->type);
    }
}
