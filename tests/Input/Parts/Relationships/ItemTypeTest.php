<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Relationships;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships\ItemType;

class ItemTypeTest extends TestCase {

    #[Group('unit')]
    public function testDefaultTypeIsItemType() {
        $itemType = new ItemType();
        
        $this->assertEquals('item_type', $itemType->type);
    }

    #[Group('unit')]
    public function testDefaultIdIsEmpty() {
        $itemType = new ItemType();
        
        $this->assertEquals('', $itemType->id);
    }

    #[Group('unit')]
    public function testSettingValidId() {
        $itemType = new ItemType();
        $itemType->id = 'model_123';
        
        $this->assertEquals('model_123', $itemType->id);
    }

    #[Group('unit')]
    public function testCannotChangeTypeFromItemType() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type must be "item_type".');
        
        $itemType = new ItemType();
        $itemType->type = 'something_else';
    }

    #[Group('unit')]
    public function testToArrayWrapsInDataStructure() {
        $itemType = new ItemType();
        $itemType->id = 'model_123';
        
        $array = $itemType->toArray();
        
        $this->assertArrayHasKey('data', $array);
        $this->assertEquals([
            'data' => [
                'type' => 'item_type',
                'id' => 'model_123',
            ]
        ], $array);
    }

    #[Group('unit')]
    public function testToArrayWithDefaultValues() {
        $itemType = new ItemType();
        
        $array = $itemType->toArray();
        
        $this->assertEquals([
            'data' => [
                'type' => 'item_type',
                'id' => '',
            ]
        ], $array);
    }

    #[Group('unit')]
    public function testTypeRemainsItemTypeAfterSettingSameValue() {
        $itemType = new ItemType();
        $itemType->type = 'item_type';
        
        $this->assertEquals('item_type', $itemType->type);
    }
}
