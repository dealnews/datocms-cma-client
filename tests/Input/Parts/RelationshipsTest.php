<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships\ItemType;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships\Creator;

class RelationshipsTest extends TestCase {

    #[Group('unit')]
    public function testDefaultInitializationCreatesItemTypeObject() {
        $relationships = new Relationships();
        
        $this->assertInstanceOf(ItemType::class, $relationships->item_type);
    }

    #[Group('unit')]
    public function testDefaultInitializationCreatesCreatorObject() {
        $relationships = new Relationships();
        
        $this->assertInstanceOf(Creator::class, $relationships->creator);
    }

    #[Group('unit')]
    public function testSettingItemTypeId() {
        $relationships = new Relationships();
        $relationships->item_type->id = 'model_123';
        
        $this->assertEquals('model_123', $relationships->item_type->id);
    }

    #[Group('unit')]
    public function testSettingCreatorTypeAndId() {
        $relationships = new Relationships();
        $relationships->creator->type = 'account';
        $relationships->creator->id = 'account_456';
        
        $this->assertEquals('account', $relationships->creator->type);
        $this->assertEquals('account_456', $relationships->creator->id);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyCreator() {
        $relationships = new Relationships();
        $relationships->item_type->id = 'model_123';
        
        $array = $relationships->toArray();
        
        $this->assertArrayNotHasKey('creator', $array);
        $this->assertArrayHasKey('item_type', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesCreatorWhenSet() {
        $relationships = new Relationships();
        $relationships->item_type->id = 'model_123';
        $relationships->creator->type = 'account';
        $relationships->creator->id = 'account_456';
        
        $array = $relationships->toArray();
        
        $this->assertArrayHasKey('creator', $array);
        $this->assertArrayHasKey('item_type', $array);
    }

    #[Group('unit')]
    public function testToArrayAlwaysIncludesItemType() {
        $relationships = new Relationships();
        
        $array = $relationships->toArray();
        
        $this->assertArrayHasKey('item_type', $array);
    }

    #[Group('unit')]
    public function testToArrayStructure() {
        $relationships = new Relationships();
        $relationships->item_type->id = 'model_123';
        $relationships->creator->type = 'user';
        $relationships->creator->id = 'user_789';
        
        $array = $relationships->toArray();
        
        $this->assertEquals([
            'item_type' => [
                'data' => [
                    'type' => 'item_type',
                    'id' => 'model_123',
                ]
            ],
            'creator' => [
                'data' => [
                    'type' => 'user',
                    'id' => 'user_789',
                ]
            ],
        ], $array);
    }

    #[Group('unit')]
    public function testToArrayStructureWithoutCreator() {
        $relationships = new Relationships();
        $relationships->item_type->id = 'model_456';
        
        $array = $relationships->toArray();
        
        $this->assertEquals([
            'item_type' => [
                'data' => [
                    'type' => 'item_type',
                    'id' => 'model_456',
                ]
            ],
        ], $array);
    }
}
