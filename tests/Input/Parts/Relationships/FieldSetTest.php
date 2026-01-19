<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Relationships;

use DealNews\DatoCMS\CMA\Input\Parts\Relationships\FieldSet;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Input\Parts\Relationships\FieldSet class
 */
class FieldSetTest extends TestCase
{
    #[Group('unit')]
    public function testDefaultValues()
    {
        $fieldset = new FieldSet();
        
        $this->assertSame('fieldset', $fieldset->type);
        $this->assertSame('', $fieldset->id);
    }

    #[Group('unit')]
    public function testTypeCanBeSetToFieldset()
    {
        $fieldset = new FieldSet();
        $fieldset->type = 'fieldset';
        
        $this->assertSame('fieldset', $fieldset->type);
    }

    #[Group('unit')]
    public function testIdCanBeSet()
    {
        $fieldset = new FieldSet();
        $fieldset->id = '24';
        
        $this->assertSame('24', $fieldset->id);
    }

    #[Group('unit')]
    public function testToArrayWrapsDataCorrectly()
    {
        $fieldset = new FieldSet();
        $fieldset->type = 'fieldset';
        $fieldset->id = '42';
        
        $result = $fieldset->toArray();
        
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
        $this->assertArrayHasKey('type', $result['data']);
        $this->assertArrayHasKey('id', $result['data']);
        $this->assertSame('fieldset', $result['data']['type']);
        $this->assertSame('42', $result['data']['id']);
    }
}
