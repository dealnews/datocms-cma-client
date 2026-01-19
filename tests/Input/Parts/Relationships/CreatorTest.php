<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Relationships;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships\Creator;

class CreatorTest extends TestCase {

    #[Group('unit')]
    public function testDefaultTypeIsNull() {
        $creator = new Creator();
        
        $this->assertNull($creator->type);
    }

    #[Group('unit')]
    public function testDefaultIdIsNull() {
        $creator = new Creator();
        
        $this->assertNull($creator->id);
    }

    #[Group('unit')]
    #[DataProvider('validCreatorTypesProvider')]
    public function testValidCreatorTypes(string $type, string $id) {
        $creator = new Creator();
        $creator->type = $type;
        $creator->id = $id;
        
        $this->assertEquals($type, $creator->type);
        $this->assertEquals($id, $creator->id);
    }

    #[Group('unit')]
    public function testTypeCanBeSetToNull() {
        $creator = new Creator();
        $creator->type = 'account';
        $creator->type = null;
        
        $this->assertNull($creator->type);
    }

    #[Group('unit')]
    public function testToArrayReturnsEmptyWhenTypeIsNull() {
        $creator = new Creator();
        $creator->id = 'some_id';
        
        $array = $creator->toArray();
        
        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    public function testToArrayReturnsEmptyWhenIdIsNull() {
        $creator = new Creator();
        $creator->type = 'account';
        
        $array = $creator->toArray();
        
        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    public function testToArrayReturnsEmptyWhenBothAreNull() {
        $creator = new Creator();
        
        $array = $creator->toArray();
        
        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    #[DataProvider('validCreatorTypesProvider')]
    public function testToArrayReturnsDataStructureWhenBothSet(string $type, string $id) {
        $creator = new Creator();
        $creator->type = $type;
        $creator->id = $id;
        
        $array = $creator->toArray();
        
        $this->assertArrayHasKey('data', $array);
        $this->assertEquals([
            'data' => [
                'type' => $type,
                'id' => $id,
            ]
        ], $array);
    }

    public static function validCreatorTypesProvider(): array {
        return [
            'account type' => ['account', 'account_123'],
            'access_token type' => ['access_token', 'token_456'],
            'user type' => ['user', 'user_789'],
            'sso_user type' => ['sso_user', 'sso_user_abc'],
            'organization type' => ['organization', 'org_xyz'],
        ];
    }

    public static function invalidCreatorTypesProvider(): array {
        return [
            'admin' => ['admin'],
            'item' => ['item'],
            'record' => ['record'],
            'random_string' => ['random_type'],
            'empty_string' => [''],
        ];
    }
}
